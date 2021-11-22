<?php

namespace App\UI\Data;

use App\Entity\Book;
use App\UI\DTO\BookDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class Mapper
{
    public const DOWNLOAD_ROUTE_NAME = 'download';

    private RequestStack $requestStack;
    private RouterInterface $router;
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;

    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
    }

    public function toDto(Book $book): BookDTO
    {
        $pdfLink = null !== $book->getFile() && true === $book->getDownloadable()
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
                true === $book->getDownloadable()
                ? $pdfLink
                : null
            )
            ->setOriginalFileName(
                null !== $book->getFile()
                ? $book->getOriginalFileName()
                : null
            )
            ->setDownloadable($book->getDownloadable())
            ->setCoverFile($coverLink);
    }

    public function mapArrayToEntity(Book $book, array $data): Book
    {
        $ignoreFields = ['id', 'author'];

        foreach ($data as $key => $value) {
            if (in_array($key, $ignoreFields)) {
                continue;
            }
            $methodName = 'set'.ucfirst($key);
            $book->$methodName($value);
        }

        return $book;
    }
}
