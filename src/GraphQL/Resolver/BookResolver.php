<?php

namespace App\GraphQL\Resolver;

use App\UI\Data\Cache;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class BookResolver implements ResolverInterface
{
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    public function getBooks(): array
    {
        return $this->cache->get();
    }
}
