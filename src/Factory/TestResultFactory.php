<?php 

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Test;
use App\Entity\TestResult;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestResultFactory
{
    public function create(Test $test): TestResult
    {
        $testResult = new TestResult();

        $fileName = sprintf('%s_%s.csv', strtolower($test->getFirstname()), strtolower($test->getLastname()));
        $tempFilePath = sys_get_temp_dir() . '/' . $fileName;

        $list = [
            ['email', $test->getEmail()],
            ['first name', $test->getFirstname()],
            ['last name', $test->getLastname()],
            ['work place', $test->getWorkplace()],
            ['submission', date_format($test->getSubmission(), "Y/m/d H:i:s")],
            ['questions count', count($test->getModule()->getQuestions())],
        ];

        $fp = fopen($tempFilePath, 'w');
        foreach ($list as $line) {
            fputcsv($fp, $line, ',');
        }
        fclose($fp);

        $uploadedFile = new UploadedFile($tempFilePath, $fileName, 'text/csv', null, true);

        $testResult->setFile($uploadedFile);
        $testResult->setTest($test);

        return $testResult;
    }
}