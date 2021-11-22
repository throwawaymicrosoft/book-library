<?php

namespace App\EventListener;

use App\Entity\Book;
use App\Service\CacheAdapter;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BookDropCacheListener
{
    private CacheAdapter $cacheAdapter;

    public function __construct(CacheAdapter $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function dropCache(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            $this->cacheAdapter->delete(Book::class);
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
