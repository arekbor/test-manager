<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\CsvGeneratorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

final class CsvGenerator implements CsvGeneratorInterface
{
    public function generate(string $fileName, array $data): \SplFileInfo
    {
        $tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->fromArray($data);

        $writer = new Csv($spreadsheet);
        $writer->save($tempFilePath);

        return new \SplFileInfo($tempFilePath);
    }
}
