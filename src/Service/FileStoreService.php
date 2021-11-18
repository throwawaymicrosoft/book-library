<?php

namespace App\Service;

use Intervention\Image\ImageManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStoreService
{
    public const COVER_RESIZE_RULES = [
        '_thumb' => 300,
    ];

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

    public function storeCover(string $directory, UploadedFile $file): string
    {
        $manager = new ImageManager();
        $fileSystem = new Filesystem();

        $uuid = Uuid::uuid1();
        $prefix = substr($uuid, 0, 2);

        if (false === $fileSystem->exists($directory.'/'.$prefix)) {
            $fileSystem->mkdir($directory.'/'.$prefix);
        }

        foreach (self::COVER_RESIZE_RULES as $key => $rule) {
            $newFileName = $directory.'/'.$prefix.'/'.$uuid.$key.'.'.$file->getClientOriginalExtension();

            $image = $manager->make($file)->resize($rule, null, function ($constraint): void {
                $constraint->aspectRatio();
            });
            $image->save($newFileName);
        }

        // Возвращаем оригинал
        $originalImage = $manager->make($file);
        $originalFileName = $directory.'/'.$prefix.'/'.$uuid.'.'.$file->getClientOriginalExtension();
        $originalImage->save($originalFileName);

        return $prefix.'/'.$uuid.'.'.$file->getClientOriginalExtension();
    }

    public function remove(string $path): void
    {
        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove(dirname($path));
        }
    }
}
