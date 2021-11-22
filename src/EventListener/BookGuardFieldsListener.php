<?php

namespace App\EventListener;

use App\Entity\Book;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BookGuardFieldsListener
{
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Book) {
            if (null === $entity->getFile()) {
                $entity->setOriginalFileName(null);
                $entity->setDownloadable(false);
            }
        }
    }
}
