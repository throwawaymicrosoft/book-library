<?php

namespace App\EventListener;

use App\Entity\Book;
use App\Service\FileStoreService;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Удаляет файлы книги и обложки при удалении сущности книги
 */
class BookOnDeleteListener
{
    private FileStoreService $fileStoreService;
    private ParameterBagInterface $parameterBag;

    public function __construct(FileStoreService $fileStoreService, ParameterBagInterface $parameterBag)
    {
        $this->fileStoreService = $fileStoreService;
        $this->parameterBag = $parameterBag;
    }
    
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            $this->fileStoreService->remove(
                $this->parameterBag->get('store.path') . '/' . $entity->getFile(),
            );
            $this->fileStoreService->remove(
                $this->parameterBag->get('cover.path') . '/' . $entity->getCover(),
            );
        }
    }
}