<?php declare(strict_types=1);

namespace App\Command;

use App\Model\MailSmtpSetting;
use App\Service\AppSettingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: "app:create-app-settings",
    description: "Creates the app settings."
)]
class CreateAppSettingsCommand extends Command
{
    public function __construct(
        private AppSettingService $appSettingService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->appSettingService->setValue('mail.smtp', new MailSmtpSetting());
        $io->success("App setting: " . MailSmtpSetting::class . " successfully created.");

        return Command::SUCCESS;
    }
}