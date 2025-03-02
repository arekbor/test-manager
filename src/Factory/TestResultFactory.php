<?php 

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Test;
use App\Entity\TestResult;
use App\Util\DateHelper;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestResultFactory
{
    public function create(Test $test): TestResult
    {
        $testResult = new TestResult();

        $fileName = sprintf('%s_%s.csv', strtolower($test->getFirstname()), strtolower($test->getLastname()));
        $tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $list = [
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

        $this->writeCsvFile($tempFilePath, $list);

        $uploadedFile = new UploadedFile($tempFilePath, $fileName, 'text/csv', null, true);

        $testResult->setFile($uploadedFile);
        $testResult->setTest($test);

        return $testResult;
    }

    private function writeCsvFile(string $filePath, array $data): void
    {
        $fp = fopen($filePath, 'w');
        if ($fp === false) {
            throw new RuntimeException("Unable to open file for writing: $filePath");
        }

        foreach ($data as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
    }
}