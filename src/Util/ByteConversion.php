<?php

declare(strict_types=1);

namespace App\Util;

final class ByteConversion
{
    public static function formatBytes(mixed $bytes, int $precision = 2): string
    {
        $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');
        $bytes = max($bytes, 0);

        $pow = floor(($bytes ? log(floatval($bytes)) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision) . ' ' . $units[$pow];
    }
}