<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\GraphQL\DTO\BookDTO;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookManager
{
    private CacheAdapter $cacheAdapter;
    private DtoMapper $dtoMapper;
    private EntityManagerInterface $entityManager;
    private FileStore $fileStore;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        CacheAdapter $cacheAdapter,
        DtoMapper $dtoMapper,
        EntityManagerInterface $entityManager,
        FileStore $fileStore,
        ParameterBagInterface $parameterBag
    ) {
        $this->cacheAdapter = $cacheAdapter;
        $this->dtoMapper = $dtoMapper;
        $this->entityManager = $entityManager;
        $this->fileStore = $fileStore;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function getCachedBooks(): array
    {
        return $this->cacheAdapter->get(Book::class, function (): array {
            return array_map(function (Book $book): BookDTO {
                return $this->dtoMapper->mapBook($book);
            }, $this->entityManager->getRepository(Book::class)->getAllBooks());
        });
    }

    private function getAuthor(string $name): Author
    {
        $author = $this->entityManager->getRepository(Author::class)
            ->findOneBy(compact('name'));

        if (null === $author) {
            $author = new Author();
            $author->setName($name);
        }

        return $author;
    }

    private function handleForm(FormInterface $data): Book
    {
        /** @var Book $book */
        $book = $data->getData();
        $book->setAuthor($this->getAuthor($data->get('author')->getData()));

        /** @var UploadedFile|null $pdfFile */
        $pdfFile = $data->get('file')->getData();
        if (null !== $pdfFile) {
            $pdfFileName = $this->fileStore->store(
                $this->parameterBag->get('store.path'),
                $pdfFile,
            );
            $book->setFile($pdfFileName);
            $book->setOriginalFileName($pdfFile->getClientOriginalName());
        }

        /** @var UploadedFile|null $bookFile */
        $coverFile = $data->get('cover')->getData();
        if (null !== $coverFile) {
            $coverFileName = $this->fileStore->storeCover(
                $this->parameterBag->get('cover.path'),
                $coverFile,
            );
            $book->setCover($coverFileName);
        }

        if ($data->has('delete_cover')) {
            if (true === $data->get('delete_cover')->getData()) {
                $this->fileStore->remove(
                    $this->parameterBag->get('cover.path').'/'.$book->getCover()
                );
                $book->setCover(null);
            }
        }
        if ($data->has('delete_file')) {
            if (true === $data->get('delete_file')->getData()) {
                $this->fileStore->remove(
                    $this->parameterBag->get('store.path').'/'.$book->getFile()
                );
                $book->setFile(null);
            }
        }

        return $book;
    }

    private function handleArray(array $data, ?Book $book = null): Book
    {
        $ignoreFields = ['id', 'author'];

        $book = $book ?? new Book();

        foreach ($data as $key => $value) {
            if (in_array($key, $ignoreFields)) {
                continue;
            }
            $methodName = 'set'.ucfirst($key);
            $book->$methodName($value);
        }

        if (isset($data['author'])) {
            $book->setAuthor($this->getAuthor($data['author']));
        }

        return $book;
    }

    public function createBook($data, ?Book $book = null): Book
    {
        if ($data instanceof Form) {
            return $this->handleForm($data);
        }

        if (is_array($data)) {
            return $this->handleArray($data, $book);
        }
    }

    public function editBook($data, Book $book): Book
    {
        return $this->createBook($data, $book);
    }
}
