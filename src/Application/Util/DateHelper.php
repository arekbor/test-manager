<?php

declare(strict_types = 1);

namespace App\Application\Util;

final class DateHelper
{
    public static function formatDateTime(\DateTimeInterface $date): string
    {
        return date_format($date, "Y/m/d H:i:s");
    }

    public static function formatDate(\DateTimeInterface $date): string
    {
        return date_format($date, "Y/m/d");
    }

    public static function diff(\DateTimeInterface $start, \DateTimeInterface $end): string
    {
        $diff = $start->diff($end);

        return sprintf('%02d:%02d:%02d', ($diff->days * 24) + $diff->h, $diff->i, $diff->s);
    }
} 