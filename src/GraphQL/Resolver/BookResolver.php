<?php

namespace App\GraphQL\Resolver;

use App\Service\BookManager;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BookResolver implements ResolverInterface
{
    private BookManager $bookManager;

    public function __construct(BookManager $bookManager)
    {
        $this->bookManager = $bookManager;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getBooks(): array
    {
        return $this->bookManager->getCachedBooks();
    }
}
