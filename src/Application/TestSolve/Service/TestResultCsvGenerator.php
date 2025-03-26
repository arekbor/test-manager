<?php

declare(strict_types = 1);

namespace App\Application\TestSolve\Service;

use App\Application\Shared\CsvGeneratorInterface;
use App\Application\Util\DateHelper;
use App\Domain\Entity\Test;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class TestResultCsvGenerator
{
    public function __construct(
        private readonly CsvGeneratorInterface $csvGenerator,
    ) {
    }

    public function create(Test $test): UploadedFile
    {
        $fileName = sprintf('%s_%s.csv', strtolower($test->getFirstname()), strtolower($test->getLastname()));
        
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

        $file = $this->csvGenerator->generate($fileName, $data);

        return new UploadedFile($file->getPathname(), $fileName, 'text/csv', test: true);
    }
}