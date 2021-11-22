<?php

namespace App\UI\Helper\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\UI\Helper\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FormHandler
{
    private EntityManagerInterface $entityManager;
    private File $file;
    private ParameterBagInterface $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, File $file, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->file = $file;
        $this->parameterBag = $parameterBag;
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

    public function handle(FormInterface $data): Book
    {
        /** @var Book $book */
        $book = $data->getData();
        $book->setAuthor($this->getAuthor($data->get('author')->getData()));

        /** @var UploadedFile|null $pdfFile */
        $pdfFile = $data->get('file')->getData();
        if (null !== $pdfFile) {
            $pdfFileName = $this->file->store(
                $this->parameterBag->get('store.path'),
                $pdfFile,
            );
            $book->setFile($pdfFileName);
            $book->setOriginalFileName($pdfFile->getClientOriginalName());
        }

        /** @var UploadedFile|null $bookFile */
        $coverFile = $data->get('cover')->getData();
        if (null !== $coverFile) {
            $coverFileName = $this->file->store(
                $this->parameterBag->get('cover.path'),
                $coverFile,
            );
            $book->setCover($coverFileName);
        }

        if ($data->has('delete_cover')) {
            if (true === $data->get('delete_cover')->getData()) {
                $this->file->remove(
                    $this->parameterBag->get('cover.path').'/'.$book->getCover()
                );
                $book->setCover(null);
            }
        }
        if ($data->has('delete_file')) {
            if (true === $data->get('delete_file')->getData()) {
                $this->file->remove(
                    $this->parameterBag->get('store.path').'/'.$book->getFile()
                );
                $book->setFile(null);
            }
        }

        return $book;
    }
}
