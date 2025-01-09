<?php

declare(strict_types=1);

namespace App\Util;

class ArrayHelper 
{
    public static function addItem(array &$array, mixed $item): void
    {
        if (!in_array($item, $array, true)) {
            $array[] = $item;
        }
    }

    public static function removeItem(array &$array, mixed $item): void
    {
        $key = array_search($item, $array, true);
        if ($key !== false) {
            unset($array[$key]);
            $array = array_values($array);
        }
    }
}