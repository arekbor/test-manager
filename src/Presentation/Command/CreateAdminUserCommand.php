<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Application\SecurityUser\Command\CreateAdminUser\CreateAdminUser;

#[AsCommand(
    name: "app:create-admin-user",
    description: "Creates an admin user based on variables in the .env file.",
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->commandBus->handle(new CreateAdminUser());
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
            return Command::FAILURE;
        }

        $io->success("Admin user successfully created.");

        return Command::SUCCESS;
    }
}
