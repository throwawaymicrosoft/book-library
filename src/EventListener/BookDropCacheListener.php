<?php

namespace App\EventListener;

use App\Entity\Book;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BookDropCacheListener
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function dropCache(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            $this->container->get('cache_adapter')->delete(Book::class);
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
