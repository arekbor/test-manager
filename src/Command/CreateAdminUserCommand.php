<?php

namespace App\Command;

use App\Entity\SecurityUser;
use App\Repository\SecurityUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Creates an admin user based on variables in the .env file.',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private SecurityUserRepository $securityUserRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private ParameterBagInterface $params,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $adminEmail = $this->params->get('app.admin_email');
        $adminPassword = $this->params->get('app.admin_password');

        if (empty($adminEmail) || empty($adminPassword)) {
            $io->error('Missing required environment variables: ADMIN_EMAIL or ADMIN_PASSWORD.');
            $io->info('Please ensure both ADMIN_EMAIL and ADMIN_PASSWORD are set in your .env configuration file.');
            return Command::FAILURE;
        }

        $existingUser = $this->securityUserRepository->findOneBy(['email' => $adminEmail]);
        if ($existingUser) {
            $io->error("A user with the email $adminEmail already exists.");
            return Command::FAILURE;
        }

        $securityUser = new SecurityUser();
        $securityUser->setEmail($adminEmail);

        $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $adminPassword);
        $securityUser->setPassword($hashedPassword);
        $securityUser->setRoles(['ROLE_ADMIN']);

        $this->em->persist($securityUser);
        $this->em->flush();

        $io->success('Admin user successfully created.');

        return Command::SUCCESS;
    }
}