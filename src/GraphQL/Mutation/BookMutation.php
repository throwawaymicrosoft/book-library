<?php

namespace App\GraphQL\Mutation;

use App\Entity\Book;
use App\UI\Data\Author;
use App\UI\Data\Mapper;
use App\UI\DTO\BookDTO;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Overblog\GraphQLBundle\Error\UserError;

/**
 * GraphQL мутации сущности книги.
 */
class BookMutation implements ResolverInterface
{
    private EntityManagerInterface $entityManager;
    private Mapper $mapper;
    private Author $author;

    public function __construct(EntityManagerInterface $entityManager, Mapper $mapper, Author $author)
    {
        $this->entityManager = $entityManager;
        $this->mapper = $mapper;
        $this->author = $author;
    }

    /**
     * GraphQL создание книги (кроме файла книги и обложки).
     */
    public function create(array $data): BookDTO
    {
        $book = $this->mapper->mapArrayToEntity(new Book(), $data);
        $book->setAuthor($this->author->get($data['author']));

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->mapper->toDto($book);
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

        $book = $this->mapper->mapArrayToEntity($book, $data);
        $book->setAuthor($this->author->get($data['author']));

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->mapper->toDto($book);
    }
}
