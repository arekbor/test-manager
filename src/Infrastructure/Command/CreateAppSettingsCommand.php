<?php 

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Model\MailSmtpAppSetting;
use App\Domain\Model\TestAppSetting;
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

        $this->createAndPersistAppSetting(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting(), $io);
        $this->createAndPersistAppSetting(TestAppSetting::APP_SETTING_KEY, new TestAppSetting(), $io);

        $this->em->flush();

        return Command::SUCCESS;
    }

    private function createAndPersistAppSetting(string $appSettingKey, object $appSettingInstance, SymfonyStyle $io): void
    {
        $appSetting = $this
            ->appSettingService
            ->setValue($appSettingKey, $appSettingInstance)
        ;

        $this->em->persist($appSetting);

        $io->success("App setting: " . $appSettingKey . " persisted.");
    }
    
}