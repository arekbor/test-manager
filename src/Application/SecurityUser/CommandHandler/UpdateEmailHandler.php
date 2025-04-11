<?php

declare(strict_types = 1);

namespace App\Application\SecurityUser\CommandHandler;

use App\Application\SecurityUser\Command\UpdateEmail;
use App\Domain\Entity\SecurityUser;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\SecurityUserEmailUnchangedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateEmailHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(UpdateEmail $command): void
    {
        $userId = $command->getUserId();

        /**
         * @var SecurityUser $securityUser
         */
        $securityUser = $this->entityManager->find(SecurityUser::class, $userId);
        if ($securityUser === null) {
            throw new NotFoundException(SecurityUser::class, ['id' => $userId]);
        }

        $updateEmailModel = $command->getUpdateEmailModel();

        if ($securityUser->getEmail() === $updateEmailModel->getEmail()) {
            throw new SecurityUserEmailUnchangedException();
        }

        $securityUser->setEmail($updateEmailModel->getEmail());
    }
}