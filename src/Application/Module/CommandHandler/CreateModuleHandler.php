<?php

declare(strict_types = 1);

namespace App\Application\Module\CommandHandler;

use App\Application\Module\Command\CreateModule;
use App\Application\Shared\RepositoryInterface;
use App\Application\Shared\UnitOfWorkInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateModuleHandler
{
    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly UnitOfWorkInterface $unitOfWork
    ) {
    }

    public function __invoke(CreateModule $command): void
    {
        $module = $command->getModule();

        $this->repository->persist($module);

        $this->unitOfWork->commit();
    }
}