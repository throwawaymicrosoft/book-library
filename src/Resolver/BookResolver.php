<?php

namespace App\Resolver;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BookResolver extends AbstractResolver
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getBooks(): array
    {
        return $this->container->get('book')->getCachedBooks();
    }
}
