<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private const NUM_OF_AUTHORS = 3;
    private const NUM_OF_BOOKS_PER_AUTHOR = 3;

    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('ru_RU');
    }

    public function load(ObjectManager $manager): void
    {


        for ($i = 1; $i <= self::NUM_OF_AUTHORS; $i++) {
            $author = new Author();
            $author->setName($this->faker->name);

            for ($j = 1; $j <= self::NUM_OF_BOOKS_PER_AUTHOR; $j++) {
                $book = new Book();
                $book->setTitle($this->faker->realText(255));
                $book->setAuthor($author);
                $book->setAllowDownload($this->faker->boolean);
                $book->setCover($this->faker->image);
                $book->setFile($this->faker->image);
                $book->setOriginalFileName($this->faker->image);
                $book->setReadAt($this->faker->dateTime);

                $manager->persist($book);
            }
        }

        $manager->flush();
    }
}
