<?php 

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Util\DateHelper as UtilDateHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    #[DataProvider('diffDatesProvider')]
    public function testDiff(\DateTimeInterface $start, \DateTimeInterface $end, string $expected): void
    {
        $diff = UtilDateHelper::diff($start, $end);

        $this->assertEquals($expected, $diff);
    }

    public static function diffDatesProvider(): array
    {
        return [
            [ new \DateTime('2025-06-10 08:15:30'), new \DateTime('2025-06-10 08:15:30'), "00:00:00" ],
            [ new \DateTime('2025-06-10 08:15:30'), new \DateTime('2025-06-10 08:16:00'), "00:00:30" ],
            [ new \DateTime('2025-06-10 12:20:15'), new \DateTime('2025-06-10 12:25:38'), "00:05:23" ],
            [ new \DateTime('2025-06-10 18:55:45'), new \DateTime('2025-06-10 19:06:28'), "00:10:43" ],
            [ new \DateTime('2025-06-10 08:15:30'), new \DateTime('2025-06-10 08:45:00'), "00:29:30" ],
            [ new \DateTime('2025-06-10 08:15:30'), new \DateTime('2025-06-10 09:15:30'), "01:00:00" ],
            [ new \DateTime('2025-06-10 08:15:30'), new \DateTime('2025-06-10 10:30:00'), "02:14:30" ],
            [ new \DateTime('2025-06-10 08:15:30'), new \DateTime('2025-06-10 08:20:15'), "00:04:45" ],
            [ new \DateTime('2025-06-10 23:59:00'), new \DateTime('2025-06-11 00:00:30'), "00:01:30" ],
            [ new \DateTime('2025-06-10 14:00:00'), new \DateTime('2025-06-10 15:20:00'), "01:20:00" ],
            [ new \DateTime('2025-06-10 07:30:00'), new \DateTime('2025-06-10 07:45:30'), "00:15:30" ],
            [ new \DateTime('2025-06-10 18:00:00'), new \DateTime('2025-06-10 18:30:45'), "00:30:45" ],
            [ new \DateTime('2025-06-10 05:10:10'), new \DateTime('2025-06-10 06:10:20'), "01:00:10" ],
            [ new \DateTime('2025-06-10 22:00:00'), new \DateTime('2025-06-10 23:05:05'), "01:05:05" ],
            [ new \DateTime('2025-06-10 12:30:45'), new \DateTime('2025-06-10 13:10:10'), "00:39:25" ],
        ];
    }
}