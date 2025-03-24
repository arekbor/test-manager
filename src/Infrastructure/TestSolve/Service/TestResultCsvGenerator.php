<?php

declare(strict_types = 1);

namespace App\Infrastructure\TestSolve\Service;

use App\Application\TestSolve\Service\TestResultCsvGeneratorInterface;
use App\Domain\Entity\Test;
use App\Util\DateHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class TestResultCsvGenerator implements TestResultCsvGeneratorInterface
{
    public function create(Test $test): \SplFileInfo
    {
        $fileName = sprintf('%s_%s.csv', strtolower($test->getFirstname()), strtolower($test->getLastname()));

        $tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $data = [
            ['ID', $test->getId()],
            ['Start', DateHelper::formatDateTime($test->getStart())],
            ['Submission', DateHelper::formatDateTime($test->getSubmission())],
            ['Diff', DateHelper::diff($test->getStart(), $test->getSubmission())],
            ['Name', sprintf('%s %s', $test->getFirstname(), $test->getLastname())],
            ['Email', $test->getEmail()],
            ['Workplace', $test->getWorkplace()],
            ['Category', $test->getModule()->getCategory()],
            ['Module name', $test->getModule()->getName()],
            ['Date of birth', $test->getDateOfBirth() ? DateHelper::formatDate($test->getDateOfBirth()) : ''],
            ['Questions count', count($test->getModule()->getQuestions())],
            ['Score', $test->getScore()],
        ];

        $fp = fopen($tempFilePath, 'w');
        if ($fp === false) {
            throw new \RuntimeException("Unable to open file for writing: $tempFilePath");
        }

        foreach ($data as $row) {
            fputcsv($fp, $row, ',', '"', '\\');
        }

        fclose($fp);

        return new UploadedFile($tempFilePath, $fileName, 'text/csv', test: true);
    }
}