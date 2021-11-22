<?php

namespace App\GraphQL\Mutation;

use App\Entity\Book;
use App\GraphQL\DTO\BookDTO;
use App\Service\BookManager;
use App\Service\DtoMapper;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Overblog\GraphQLBundle\Error\UserError;

/**
 * GraphQL мутации сущности книги.
 */
class BookMutation implements ResolverInterface
{
    private BookManager $bookManager;
    private EntityManagerInterface $entityManager;
    private DtoMapper $dtoMapper;

    public function __construct(BookManager $bookManager, EntityManagerInterface $entityManager, DtoMapper $dtoMapper)
    {
        $this->bookManager = $bookManager;
        $this->entityManager = $entityManager;
        $this->dtoMapper = $dtoMapper;
    }

    /**
     * GraphQL создание книги (кроме файла книги и обложки).
     */
    public function create(array $data): BookDTO
    {
        $book = $this->bookManager->createBook($data);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->dtoMapper->mapBook($book);
    }

    /**
     * GraphQL редактирование данных книги (кроме файла книги и обложки).
     */
    public function edit(array $data): BookDTO
    {
        /** @var Book|null $book */
        $book = $this->entityManager->getRepository(Book::class)->find($data['id']);
        if (null === $book) {
            throw new UserError(sprintf('Could not find Book#%d', $data['id']));
        }

        $book = $this->bookManager->editBook($data, $book);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->dtoMapper->mapBook($book);
    }
}
