<?php

namespace App\EventListener;

use App\Entity\Book;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Удаляет файлы книги и обложки при удалении сущности книги.
 */
class BookOnDeleteListener
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            if (null !== $entity->getFile()) {
                $this->container->get('file_store')->remove(
                    $this->container->getParameter('store.path').'/'.$entity->getFile(),
                );
            }

            if (null !== $entity->getCover()) {
                $this->container->get('file_store')->remove(
                    $this->container->getParameter('cover.path').'/'.$entity->getCover(),
                );
            }
        }
    }
}
