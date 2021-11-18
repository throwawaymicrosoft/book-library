<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageThumbnail extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('thumbnail', [$this, 'getImage']),
        ];
    }

    public function getImage(string $image, string $type): string
    {
        $imagePath = explode('.', $image)[0];
        $imageExtension = '.'.explode('.', $image)[1];

        if ('_' !== $type[0]) {
            $type = '_'.$type;
        }

        return sprintf('%s%s%s',
            $imagePath,
            $type,
            $imageExtension
        );
    }
}
