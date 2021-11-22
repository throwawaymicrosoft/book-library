<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookEditType;
use App\Form\BookType;
use App\UI\Data\Author as AuthorHelper;
use App\UI\Data\Cache;
use App\UI\DTO\BookDTO;
use App\UI\Helper\File\Thumbnail;
use App\UI\Helper\Form\FormHandler;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(Cache $cache): Response
    {
        try {
            /** @var BookDTO[] $books */
            $books = $cache->get();
        } catch (InvalidArgumentException|NotFoundExceptionInterface $e) {
            throw new ServiceUnavailableHttpException();
        }

        return $this->render('book/index.html.twig', compact('books'));
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(
        Request $request,
        FormHandler $formHandler,
        ParameterBagInterface $parameterBag,
        Thumbnail $thumbnails,
        AuthorHelper $authorHelper
    ): Response {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $formHandler->handle($form);

            if (null !== $form->get('cover')->getData()) {
                $thumbnails->generate(
                    $parameterBag->get('cover.path').'/'.$book->getCover()
                );
            }

            $book->setAuthor(
                $authorHelper->get(
                    $form->get('author')->getData()
                )
            );

            $this->getDoctrine()->getManager()->persist($book);
            $this->getDoctrine()->getManager()->flush();

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
     */
    public function edit(
        Request $request,
        Book $book,
        FormHandler $formHandler,
        Thumbnail $thumbnails,
        ParameterBagInterface $parameterBag,
        AuthorHelper $authorHelper
    ): Response {
        $form = $this->createForm(BookEditType::class, $book);

        // Pre-populate form values of unmapped fields
        $form->get('author')->setData($book->getAuthor());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $formHandler->handle($form);

            if (null !== $form->get('cover')->getData()) {
                $thumbnails->generate(
                    $parameterBag->get('cover.path').'/'.$book->getCover()
                );
            }

            if ($form->has('author')) {
                $book->setAuthor(
                    $authorHelper->get(
                        $form->get('author')->getData()
                    )
                );
            }

            $this->getDoctrine()->getManager()->persist($book);
            $this->getDoctrine()->getManager()->flush();

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
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index', [], Response::HTTP_SEE_OTHER);
    }
}
