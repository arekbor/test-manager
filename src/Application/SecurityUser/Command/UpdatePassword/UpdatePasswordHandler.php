<?php

declare(strict_types=1);

namespace App\Application\SecurityUser\Command\UpdatePassword;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\SecurityUser;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\SecurityUserInvalidCurrentPassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdatePasswordHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {}

    public function __invoke(UpdatePassword $command): void
    {
        $userId = $command->getUserId();

        /**
         * @var SecurityUser $securityUser
         */
        $securityUser = $this->entityManager->find(SecurityUser::class, $userId);
        if ($securityUser === null) {
            throw new NotFoundException(SecurityUser::class, ['id' => $userId]);
        }

        $updatePasswordModel = $command->getUpdatePasswordModel();

        if (!$this->userPasswordHasher->isPasswordValid($securityUser, $updatePasswordModel->getCurrentPassword())) {
            throw new SecurityUserInvalidCurrentPassword();
        }

        $newPassword = $this->userPasswordHasher->hashPassword($securityUser, $updatePasswordModel->getPassword());

        try {
            $securityUser->setPassword($newPassword);
        } catch (\Throwable $ex) {
            $this->entityManager->refresh($securityUser);
            throw $ex;
        }
    }
}
