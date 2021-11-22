<?php

namespace App\EventListener;

use App\Entity\Book;
use App\UI\Data\Cache;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BookDropCacheListener
{
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function dropCache(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            $this->cache->delete(Book::class);
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->dropCache($args);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->dropCache($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->dropCache($args);
    }
}
