<?php

namespace App\UI\Data;

use App\Entity\Book;
use App\UI\DTO\BookDTO;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Cache
{
    private CacheInterface $cache;
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;
    private Mapper $mapper;

    public function __construct(
        CacheInterface $cache,
        Mapper $mapper,
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $entityManager
    ) {
        $this->cache = $cache;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->mapper = $mapper;
    }

    private function getKey(): string
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '', BookDTO::class);
    }

    /**
     * @return mixed
     *
     * @throws NotFoundExceptionInterface|InvalidArgumentException
     */
    public function get()
    {
        return $this->cache->get($this->getKey(), function (ItemInterface $item) {
            $item->expiresAfter((int) $this->parameterBag->get('cache.ttl'));

            return array_map(function (Book $book): BookDTO {
                return $this->mapper->toDto($book);
            }, $this->entityManager->getRepository(Book::class)->getAllBooks());
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(): void
    {
        $this->cache->delete($this->getKey());
    }
}
