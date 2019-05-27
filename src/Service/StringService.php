<?php

namespace App\Service;

class StringService
{
    public static function checkCharactersAndNumbers(string $str): bool
    {
        if (1 !== preg_match('/^[a-z0-9]+$/i', $str)) {
            throw new \Exception('Tampering field content.');
        }

        return true;
    }

    public static function checkCharactersAndNumbersWithDot(string $str): bool
    {
        if (1 !== preg_match('/^[a-z0-9]+.[a-z0-9]+$/i', $str)) {
            throw new \Exception('Tampering field content.');
        }

        return true;
    }

    public static function checkFilename(string $fileName): string
    {
        $fileName = str_replace('\\', '/', $fileName);
        $fileName = basename($fileName);
        if ('' === $fileName) {
            throw new \Exception('Tampering field content.');
        }

        return $fileName;
    }
}
