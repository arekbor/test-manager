<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\CsvGeneratorInterface;
use SplFileInfo;

final class CsvGenerator implements CsvGeneratorInterface
{
    public function generate(string $fileName, array $data): SplFileInfo
    {
        $tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $fp = fopen($tempFilePath, 'w');
        if ($fp === false) {
            throw new \RuntimeException("Unable to open file for writing: $tempFilePath");
        }

        foreach ($data as $row) {
            fputcsv($fp, $row, ',', '"', '\\');
        }

        fclose($fp);

        return new SplFileInfo($tempFilePath);
    }
}