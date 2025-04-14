<?php

declare(strict_types = 1);

namespace App\Application\Test\QueryHandler;

use App\Application\Shared\VichFileHandlerInterface;
use App\Application\Test\Query\GetTestResultFile;
use App\Domain\Entity\TestResult;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetTestResultFileHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VichFileHandlerInterface $vichFileHandler
    ) {
    }

    public function __invoke(GetTestResultFile $query): \SplFileInfo
    {
        $testResultId = $query->getTestResultId();

        /**
         * @var TestResult $testResult
         */
        $testResult = $this->entityManager->find(TestResult::class, ['id' => $testResultId]);
        if ($testResult === null) {
            throw new NotFoundException(TestResult::class, ['id' => $testResultId]);
        }

        return $this->vichFileHandler->handle($testResult, TestResult::FILE_FIELD_NAME);
    }
}