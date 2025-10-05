<?php

declare(strict_types=1);

namespace App\Application\SecurityUser\Command\CreateAdminUser;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateAdminUserHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ParameterBagInterface $parameterBag
    ) {}

    public function __invoke(CreateAdminUser $command): void
    {
        $email = $this->parameterBag->get('app.admin_email');
        $password = $this->parameterBag->get('app.admin_password');

        $securityUser = new SecurityUser();

        $hashedPassowrd = $this->userPasswordHasher->hashPassword($securityUser, $password);

        $securityUser->setEmail($email);
        $securityUser->setPassword($hashedPassowrd);
        $securityUser->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($securityUser);
    }
}
