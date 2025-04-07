<?php

declare(strict_types = 1);

namespace App\Application\Test\CommandHandler;

use App\Application\Test\Command\CreateTest;
use App\Domain\Entity\Module;
use App\Domain\Entity\SecurityUser;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateTestHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(CreateTest $command): void
    {
        $moduleId = $command->getModuleId();

        /**
         * @var Module $module
         */
        $module = $this->entityManager->find(Module::class, $moduleId);
        if ($module === null) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        $creatorEmail = $command->getCreatorEmail();

        /**
         * @var SecurityUser $securityUser
         */
        $securityUser = $this->entityManager->getRepository(SecurityUser::class)->findOneBy(['email' => $creatorEmail]);
        if ($securityUser === null) {
            throw new NotFoundException(SecurityUser::class, ['email' => $creatorEmail]);
        }

        $test = new Test();
        $test->setExpiration($command->getTestModel()->getExpiration());
        $test->setModule($module);
        $test->setCreator($securityUser);

        $this->entityManager->persist($test);
    }
}