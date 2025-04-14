<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Test\Command\DeleteTest;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteTestHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(DeleteTest $command): void
    {
        $testId = $command->getTestId();

        /**
         * @var Test $test
         */
        $test = $this->entityManager->find(Test::class, $testId);
        if ($test === null) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }

        $this->entityManager->remove($test);
    }
}