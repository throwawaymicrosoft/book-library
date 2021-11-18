<?php

namespace App\GraphQL\DTO;

use DateTimeInterface;

class BookDTO
{
    public int $id;
    public string $title;
    public string $author;
    public DateTimeInterface $readAt;
    public ?string $pdfFile = null;
    public ?string $originalFileName = null;
    public bool $allowDownload;
    public ?string $coverFile = null;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return BookDTO
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return BookDTO
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return BookDTO
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getReadAt(): DateTimeInterface
    {
        return $this->readAt;
    }

    /**
     * @return $this
     */
    public function setReadAt(DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;

        return $this;
    }

    public function getPdfFile(): ?string
    {
        return $this->pdfFile;
    }

    /**
     * @return BookDTO
     */
    public function setPdfFile(?string $pdfFile): self
    {
        $this->pdfFile = $pdfFile;

        return $this;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName(?string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

    public function isAllowDownload(): bool
    {
        return $this->allowDownload;
    }

    /**
     * @return BookDTO
     */
    public function setAllowDownload(bool $allowDownload): self
    {
        $this->allowDownload = $allowDownload;

        return $this;
    }

    public function getCoverFile(): ?string
    {
        return $this->coverFile;
    }

    /**
     * @return BookDTO
     */
    public function setCoverFile(?string $coverLink): self
    {
        $this->coverFile = $coverLink;

        return $this;
    }
}
