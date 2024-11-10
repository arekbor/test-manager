<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\AppSetting;
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
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $mailSmtpAppSetting = $this->createMailSmtpAppSetting();
        $this->em->persist($mailSmtpAppSetting);
        
        $this->em->flush();

        $io->success("App setting: {$mailSmtpAppSetting->getKey()} successfully created.");

        return Command::SUCCESS;
    }

    private function createMailSmtpAppSetting(): AppSetting
    {
        $setting = new AppSetting();
        return $setting
            ->setKey('mail.smtp')
            ->setValue([
                'server_address' => '',
                'server_port' => '',
                'from_address' => '',
                'name' => '',
                'password' => ''
            ])
        ;
    }
}