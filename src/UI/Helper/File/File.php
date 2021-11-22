<?php

namespace App\UI\Helper\File;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File
{
    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function store(string $directory, UploadedFile $file): string
    {
        $uuid = Uuid::uuid1().'.'.$file->getClientOriginalExtension();
        $prefix = substr($uuid, 0, 2);

        try {
            $file->move($directory.'/'.$prefix, $uuid);
        } catch (FileException $e) {
            throw new FileException($e);
        }

        return $prefix.'/'.$uuid;
    }

    public function remove(string $path): void
    {
        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove(dirname($path));
        }
    }
}
