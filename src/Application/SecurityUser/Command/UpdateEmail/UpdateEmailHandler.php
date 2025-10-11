<?php

declare(strict_types=1);

namespace App\Application\SecurityUser\Command\UpdateEmail;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\SecurityUser;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\SecurityUserEmailUnchangedException;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateEmailHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(UpdateEmail $command): void
    {
        $userId = $command->getUserId();

        /**
         * @var SecurityUser|null $securityUser
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
