<?php

namespace App\Service;

class PathService
{
    public static function pathStripLastPart(string $path): string
    {
        $slug = explode('/', $path);
        array_pop($slug);
        $path = implode('/', $slug);

        return $path;
    }
}
