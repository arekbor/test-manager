<?php 

declare(strict_types=1);

namespace App\Command;

use App\Model\MailSmtpAppSetting;
use App\Service\AppSettingService;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $appSetting = $this
            ->appSettingService
            ->setValue(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting())
        ;

        $this->em->persist($appSetting);
        $io->success("App setting: " .MailSmtpAppSetting::APP_SETTING_KEY. " persisted.");

        $this->em->flush();

        return Command::SUCCESS;
    }
}