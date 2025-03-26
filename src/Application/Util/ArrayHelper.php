<?php

declare(strict_types = 1);

namespace App\Application\Util;

final class ArrayHelper 
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

    public static function findFirstByProperty(array $array, string $method, mixed $value): mixed
    {
        $filtered = array_filter($array, fn($item) => method_exists($item, $method) && $item->$method() === $value);
        return $filtered ? reset($filtered) : null;
    }
}