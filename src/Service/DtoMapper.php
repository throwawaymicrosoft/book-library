<?php

namespace App\Service;

use App\Entity\Book;
use App\GraphQL\DTO\BookDTO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class DtoMapper
{
    public const DOWNLOAD_ROUTE_NAME = 'download';

    private RequestStack $requestStack;
    private RouterInterface $router;
    private ParameterBagInterface $parameterBag;

    public function __construct(RequestStack $requestStack, RouterInterface $router, ParameterBagInterface $parameterBag)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->parameterBag = $parameterBag;
    }

    public function mapBook(Book $book): BookDTO
    {
        $pdfLink = null !== $book->getFile() && true === $book->getAllowDownload()
            ? $this->requestStack->getMasterRequest()->getSchemeAndHttpHost().
            $this->router->generate(self::DOWNLOAD_ROUTE_NAME, ['id' => $book->getId()])
            : null;

        $coverLink = null !== $book->getCover()
            ? sprintf(
                '%s%s/%s',
                $this->requestStack->getMasterRequest()->getSchemeAndHttpHost(),
                $this->parameterBag->get('cover.public_path'),
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
