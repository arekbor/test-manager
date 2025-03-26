<?php 

declare(strict_types=1);

namespace App\Tests\Presentation;

use App\Presentation\Util\ByteConversion;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ByteConversionTest extends TestCase
{
    #[DataProvider('bytesProvider')]
    public function testFormatBytes($inputBytes, $expectedOutput, $precision = 2)
    {
        $result = ByteConversion::formatBytes($inputBytes, $precision);
        $this->assertSame($expectedOutput, $result);
    }

    public static function bytesProvider(): array
    {
        return [
            [0, '0.00 B'],
            [1, '1.00 B'],
            [1024, '1.00 KiB'],
            [1048576, '1.00 MiB'],
            [1348576, '1.29 MiB'],
            [1073741824, '1.00 GiB'],
            [1473741824, '1.37 GiB'],
            [1099511627776, '1.00 TiB'],
            [1536, '1.50 KiB'],
            [1572864, '1.50 MiB'],

            [1536, '1.5 KiB', 1],
            [1536, '1.500 KiB', 3],
            [1699511627776, '1.5457 TiB', 4],

            [-1024, '0.00 B'],
        ];
    }
}