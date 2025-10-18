<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: "app:init-database",
    description: "Inits database - creates when database not exists nad makes migrations"
)]
final class InitDatabaseCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $io->title("INIT-DATABASE");

        $io->section('Checks connection with database...');

        $arrayInput = new ArrayInput([
            'command' => 'doctrine:database:create'
        ]);

        $statusCode = $application->run($arrayInput, $output);
        if ($statusCode === Command::SUCCESS) {
            $io->info("Database successfully created.");

            $io->section("Execute migrations...");

            $arrayInput = new ArrayInput([
                'command' => 'doctrine:migrations:migrate',
                '--no-interaction' => true,
            ]);

            $application->run($arrayInput, $output);
        }

        return Command::SUCCESS;
    }
}
