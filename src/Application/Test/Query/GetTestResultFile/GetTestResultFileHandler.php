<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetTestResultFile;

use App\Application\Shared\Bus\QueryBusHandlerInterface;
use App\Application\Shared\VichFileHandlerInterface;
use App\Domain\Entity\TestResult;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class GetTestResultFileHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VichFileHandlerInterface $vichFileHandler
    ) {}

    public function __invoke(GetTestResultFile $query): \SplFileInfo
    {
        $testResultId = $query->getTestResultId();

        /**
         * @var TestResult|null $testResult
         */
        $testResult = $this->entityManager->find(TestResult::class, ['id' => $testResultId]);
        if ($testResult === null) {
            throw new NotFoundException(TestResult::class, ['id' => $testResultId]);
        }

        return $this->vichFileHandler->handle($testResult, TestResult::FILE_FIELD_NAME);
    }
}
