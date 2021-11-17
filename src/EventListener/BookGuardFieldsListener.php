<?php

namespace App\EventListener;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BookGuardFieldsListener
{
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            if ($entity->getFile() === null) {
                $entity->setOriginalFileName(null);
                $entity->setAllowDownload(false);
            }
        }
    }
}