<?php

declare(strict_types=1);

namespace App\Infrastructure\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeTzType;

class DefaultDateTimeTimezoneType extends DateTimeTzType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $dateTime = parent::convertToPHPValue($value, $platform);

        $tz = date_default_timezone_get();

        if ($dateTime instanceof \DateTime) {
            $dateTime = $dateTime->setTimezone(new \DateTimeZone($tz));
        }

        return $dateTime;
    }
}