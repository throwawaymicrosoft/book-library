<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookEditType;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\FileStoreService;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route("/")
 */
class BookController extends AbstractController
{
    private const CACHE_KEY = 'books_cache';

    /**
     * @Route("/", name="book_index", methods={"GET"})
     * @throws InvalidArgumentException
     */
    public function index(BookRepository $bookRepository, CacheInterface $cache): Response
    {
        $books = $cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($bookRepository): array {
            $ttl = (int)$this->getParameter('cache.ttl');
            $item->expiresAfter($ttl);

            return $bookRepository->findAll();
        });

        $books = $bookRepository->findBy([], [
            'read_at' => 'DESC'
        ]);

        return $this->render('book/index.html.twig', compact('books'));
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     *
     * @throws InvalidArgumentException
     */
    public function new(Request $request, FileStoreService $fileStoreService, CacheInterface $cache): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $authorEntity = $entityManager->getRepository(Author::class)->findOneBy([
                'name' => $form->get('author')->getData(),
            ]);
            if ($authorEntity === null) {
                $authorEntity = new Author();
                $authorEntity->setName(
                    $form->get('author')->getData()
                );
            }
            $book->setAuthor($authorEntity);

            /** @var UploadedFile $bookFile */
            $bookFile = $form->get('file')->getData();
            /** @var UploadedFile $bookFile */
            $coverFile = $form->get('cover')->getData();

            $fileName = $fileStoreService->store(
                $this->getParameter('store.path'),
                $bookFile,
            );
            $coverName = $fileStoreService->storeCover(
                $this->getParameter('cover.path'),
                $coverFile,
            );

            $book->setFile($fileName);
            $book->setOriginalFileName($bookFile->getClientOriginalName());
            $book->setCover($coverName);

            $entityManager->persist($book);
            $entityManager->flush();

            $cache->delete(self::CACHE_KEY);

            return $this->redirectToRoute('book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     *
     * @throws InvalidArgumentException
     */
    public function edit(Request $request, Book $book, CacheInterface $cache, FileStoreService $fileStoreService): Response
    {
        $form = $this->createForm(BookEditType::class, $book);

        // Pre-populate form values of unmapped fields
        $form->get('author')->setData($book->getAuthor());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete_cover')->getData() === true) {
                $fileStoreService->remove(
                    $this->getParameter('cover.path') . '/' . $book->getCover()
                );
                $book->setCover(null);
            }
            if ($form->get('delete_file')->getData() === true) {
                $fileStoreService->remove(
                    $this->getParameter('store.path') . '/' . $book->getFile()
                );
                $book->setFile(null);
            }

            /** @var UploadedFile $bookFile */
            $coverFile = $form->get('cover')->getData();
            if ($coverFile) {
                $fileStoreService->remove(
                    $this->getParameter('cover.path') . '/' . $book->getCover()
                );
                $book->setCover(
                    $fileStoreService->store(
                        $this->getParameter('cover.path'),
                        $coverFile,
                    ));
            }

            /** @var UploadedFile $bookFile */
            $bookFile = $form->get('file')->getData();
            if ($bookFile) {
                $fileStoreService->remove(
                    $this->getParameter('store.path') . '/' . $book->getFile(),
                );
                $book->setFile(
                    $fileStoreService->store(
                        $this->getParameter('store.path'),
                        $bookFile,
                    ));
                $book->setOriginalFileName($bookFile->getClientOriginalName());
            }

            $this->getDoctrine()->getManager()->flush();
            $cache->delete(self::CACHE_KEY);

            return $this->redirectToRoute('book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     *
     * @throws InvalidArgumentException
     */
    public function delete(Request $request, Book $book, CacheInterface $cache): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();

            $cache->delete(self::CACHE_KEY);
        }

        return $this->redirectToRoute('book_index', [], Response::HTTP_SEE_OTHER);
    }
}
