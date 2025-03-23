<?php 

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Command\CreateAppSetting;
use App\Domain\Model\MailSmtpAppSetting;
use App\Domain\Model\TestAppSetting;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: "app:create-app-settings",
    description: "Creates the app settings."
)]
class CreateAppSettingsCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->commandBus->dispatch(new CreateAppSetting(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting()));
            $this->commandBus->dispatch(new CreateAppSetting(TestAppSetting::APP_SETTING_KEY, new TestAppSetting()));
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
            return Command::FAILURE;
        }

        $io->success("Successfully created app settings.");

        return Command::SUCCESS;
    }
}