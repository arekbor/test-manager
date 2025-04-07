<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Test\Command\UpdateTest;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateTestHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(UpdateTest $command): void
    {
        $testId = $command->getTestId();

        /**
         * @var Test $test
         */
        $test = $this->entityManager->find(Test::class, $testId);
        if ($test === null) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        $test->setExpiration($command->getTestModel()->getExpiration());
    }
}