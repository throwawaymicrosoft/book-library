<?php

namespace App\UI\Data;

use App\Entity\Author as AuthorEntity;
use Doctrine\ORM\EntityManagerInterface;

class Author
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(string $name): AuthorEntity
    {
        $author = $this->entityManager->getRepository(AuthorEntity::class)
            ->findOneBy(compact('name'));

        if (null === $author) {
            $author = new AuthorEntity();
            $author->setName($name);
        }

        return $author;
    }
}
