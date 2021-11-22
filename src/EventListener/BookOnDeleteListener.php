<?php

namespace App\EventListener;

use App\Entity\Book;
use App\Service\FileStore;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Удаляет файлы книги и обложки при удалении сущности книги.
 */
class BookOnDeleteListener
{
    private FileStore $fileStore;
    private ParameterBagInterface $parameterBag;

    public function __construct(FileStore $fileStore, ParameterBagInterface $parameterBag)
    {
        $this->fileStore = $fileStore;
        $this->parameterBag = $parameterBag;
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            if (null !== $entity->getFile()) {
                $this->fileStore->remove(
                    $this->parameterBag->get('store.path').'/'.$entity->getFile(),
                );
            }

            if (null !== $entity->getCover()) {
                $this->fileStore->remove(
                    $this->parameterBag->get('cover.path').'/'.$entity->getCover(),
                );
            }
        }
    }
}
