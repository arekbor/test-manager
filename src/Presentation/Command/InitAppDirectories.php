<?php

declare(strict_types=1);

namespace App\Presentation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: "app:init-app-directories",
    description: "Inits app directories - creates a necessary app directories."
)]
final class InitAppDirectories extends Command
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $appBasePath = $this->parameterBag->get('app.base.path');
        if (!file_exists($appBasePath)) {
            $io->error("App base path not exists.");
            return Command::FAILURE;
        }

        /**
         * @var array<string> $pathsToCreate
         */
        $pathsToCreate = [
            $this->parameterBag->get('app.videos.path'),
            $this->parameterBag->get('app.testResults.path')
        ];

        foreach ($pathsToCreate as $path) {
            if (!file_exists($path)) {
                if (!mkdir($path)) {
                    $io->error(sprintf("Error while creating directory '%s'.", $path));
                    return Command::FAILURE;
                } else {
                    $io->info(sprintf("Successfully created directory '%s'.", $path));
                }
            }
        }

        return Command::SUCCESS;
    }
}
