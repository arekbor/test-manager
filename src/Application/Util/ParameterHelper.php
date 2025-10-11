<?php

declare(strict_types=1);

namespace App\Application\Util;

final class ParameterHelper
{
    /**
     * @return array<string>
     */
    public static function explodeStringToArray(string $param, string $separator = '|'): array
    {
        return explode($separator, $param);
    }
}
