<?php

namespace App\GraphQL\Mutation;

use App\Entity\Book;
use App\GraphQL\DTO\BookDTO;
use Overblog\GraphQLBundle\Error\UserError;

/**
 * GraphQL мутации сущности книги.
 */
class BookMutation extends AbstractMutation
{
    /**
     * GraphQL создание книги (кроме файла книги и обложки).
     */
    public function create(array $data): BookDTO
    {
        $book = $this->container->get('book')->createBook($data);

        $this->getDoctrine()->persist($book);
        $this->getDoctrine()->flush();

        return $this->container->get('dto.mapper')
            ->mapBook($book, );
    }

    /**
     * GraphQL редактирование данных книги (кроме файла книги и обложки).
     */
    public function edit(array $data): BookDTO
    {
        /** @var Book|null $book */
        $book = $this->getDoctrine()->getRepository(Book::class)->find($data['id']);
        if (null === $book) {
            throw new UserError(sprintf('Could not find Book#%d', $data['id']));
        }

        $book = $this->container->get('book')->editBook($data, $book);

        $this->getDoctrine()->persist($book);
        $this->getDoctrine()->flush();

        return $this->container->get('dto.mapper')
            ->mapBook($book, );
    }
}
