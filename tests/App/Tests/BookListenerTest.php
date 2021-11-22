<?php

namespace App\Tests;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class BookListenerTest extends KernelTestCase
{
    private const IMAGE_FILENAME = 'test.png';
    private const PDF_FILENAME = 'test.pdf';

    protected function setUp(): void
    {
        self::bootKernel();

        $this->filesystem = new Filesystem();

        $this->coverpath = self::$container
            ->getParameter('cover.path');
        $this->storepath = self::$container
            ->getParameter('store.path');

        $this->entityManager = self::$container
            ->get('doctrine')
            ->getManager();

        $this->filesystem->copy(
            'tests/App/Tests/' . self::IMAGE_FILENAME,
            $this->coverpath . '/test/' . self::IMAGE_FILENAME,
            true
        );
        $this->filesystem->copy(
            'tests/App/Tests/' . self::PDF_FILENAME,
            $this->storepath . '/test/' . self::PDF_FILENAME,
            true
        );
    }

    private function getBook(): Book
    {
        $book = new Book();
        $book->setTitle('test');
        $book->setAuthor((new Author())->setName('test name'));
        $book->setFile('test/' . self::PDF_FILENAME);
        $book->setCover('test/' . self::IMAGE_FILENAME);
        $book->setAllowDownload(true);
        $book->setOriginalFileName('test original');

        return $book;
    }

    public function testNullValues(): void
    {
        $book = $this->getBook();
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $this->assertNotNull($book->getOriginalFileName());
        $this->assertNotNull($book->getFile());
        $this->assertTrue($book->getAllowDownload());

        $book->setFile(null);
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $this->assertNull($book->getOriginalFileName());
        $this->assertNull($book->getFile());
        $this->assertFalse($book->getAllowDownload());
    }

    public function testDoctrineListenerDelete(): void
    {
        $book = $this->getBook();
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $this->assertTrue($this->filesystem->exists($this->coverpath . '/' . $book->getCover()));
        $this->assertTrue($this->filesystem->exists($this->storepath . '/' . $book->getFile()));

        $this->entityManager->remove($book);
        $this->entityManager->flush();
        $this->assertFalse($this->filesystem->exists($this->coverpath . '/' . $book->getCover()));
        $this->assertFalse($this->filesystem->exists($this->storepath . '/' . $book->getFile()));

    }
}
