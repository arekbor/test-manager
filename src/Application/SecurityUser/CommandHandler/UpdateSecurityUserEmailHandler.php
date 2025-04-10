<?php

declare(strict_types = 1);

namespace App\Application\SecurityUser\CommandHandler;

use App\Application\SecurityUser\Command\UpdateSecurityUserEmail;
use App\Domain\Entity\SecurityUser;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\SecurityUserEmailUnchangedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateSecurityUserEmailHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(UpdateSecurityUserEmail $command): void
    {
        $userId = $command->getUserId();

        /**
         * @var SecurityUser $securityUser
         */
        $securityUser = $this->entityManager->find(SecurityUser::class, $userId);
        if ($securityUser === null) {
            throw new NotFoundException(SecurityUser::class, ['id' => $userId]);
        }

        $updateEmail = $command->getUpdateEmail();

        if ($securityUser->getEmail() === $updateEmail->getEmail()) {
            throw new SecurityUserEmailUnchangedException();
        }

        $securityUser->setEmail($updateEmail->getEmail());
    }
}