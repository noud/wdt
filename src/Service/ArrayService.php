<?php

namespace App\Service;

class ArrayService
{
    public static function searchArrayForId($id, $keyname, $array)
    {
        foreach ($array as $key => $val) {
            if ($val[$keyname] === $id) {
                return $key;
            }
        }

        return null;
    }
}
