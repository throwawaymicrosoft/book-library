<?php

namespace App\Service;

use App\Entity\Book;
use App\GraphQL\DTO\BookDTO;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DtoMapperService
{
    public const DOWNLOAD_ROUTE_NAME = 'download';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function mapBook(Book $book): BookDTO
    {
        $pdfLink = null !== $book->getFile() && true === $book->getAllowDownload()
            ? $this->container->get('request_stack')->getMasterRequest()->getSchemeAndHttpHost().
            $this->container->get('router')->generate(self::DOWNLOAD_ROUTE_NAME, ['id' => $book->getId()])
            : null;

        $coverLink = null !== $book->getCover()
            ? sprintf(
                '%s%s/%s',
                $this->container->get('request_stack')->getMasterRequest()->getSchemeAndHttpHost(),
                $this->container->getParameter('cover.public_path'),
                $book->getCover()
            )
            : null;

        return (new BookDTO())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setAuthor($book->getAuthor())
            ->setReadAt($book->getReadAt())
            ->setPdfFile(
                true === $book->getAllowDownload()
                ? $pdfLink
                : null
            )
            ->setOriginalFileName(
                null !== $book->getFile()
                ? $book->getOriginalFileName()
                : null
            )
            ->setAllowDownload($book->getAllowDownload())
            ->setCoverFile($coverLink);
    }
}
