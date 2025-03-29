<?php

declare(strict_types = 1);

namespace App\Application\SecurityUser\CommandHandler;

use App\Application\SecurityUser\Command\CreateAdminUser;
use App\Application\Shared\RepositoryInterface;
use App\Domain\Entity\SecurityUser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateAdminUserHandler
{
    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function __invoke(CreateAdminUser $command): void
    {
        $email = $this->parameterBag->get('app.admin_email');
        $password = $this->parameterBag->get('app.admin_password');

        $securityUser = new SecurityUser();

        $hashedPassowrd = $this->userPasswordHasher->hashPassword($securityUser, $password);

        $securityUser->setEmail($email);
        $securityUser->setPassword($hashedPassowrd);
        $securityUser->setRoles(['ROLE_ADMIN']);

        $this->repository->persist($securityUser);
    }
}