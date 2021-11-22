<?php

namespace App\UI\Helper\File;

use Intervention\Image\ImageManager;
use Symfony\Component\Filesystem\Filesystem;

class Thumbnail
{
    public const COVER_RESIZE_RULES = [
        '_thumb' => 100,
    ];

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function generate(string $pathToImage): void
    {
        $targetDir = dirname($pathToImage);

        if ($this->filesystem->exists($targetDir)) {
            $manager = new ImageManager();

            $originFileName = pathinfo($pathToImage, PATHINFO_FILENAME);
            $originFileExtension = pathinfo($pathToImage, PATHINFO_EXTENSION);

            foreach (self::COVER_RESIZE_RULES as $key => $rule) {
                $newFileName = $targetDir.'/'.$originFileName.$key.'.'.$originFileExtension;

                $image = $manager->make($pathToImage)->resize($rule, null, function ($constraint): void {
                    $constraint->aspectRatio();
                });
                $image->save($newFileName);
            }
        }
    }
}
