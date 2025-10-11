<?php

declare(strict_types=1);

namespace App\Application\Test\Command\CreateTest;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Module;
use App\Domain\Entity\SecurityUser;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final class CreateTestHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(CreateTest $command): void
    {
        $moduleId = $command->getModuleId();

        /**
         * @var Module|null $module
         */
        $module = $this->entityManager->find(Module::class, $moduleId);
        if ($module === null) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        $creatorId = $command->getCreatorId();

        /**
         * @var SecurityUser|null $securityUser
         */
        $securityUser = $this->entityManager->find(SecurityUser::class, $creatorId);
        if ($securityUser === null) {
            throw new NotFoundException(SecurityUser::class, ['id' => $creatorId]);
        }

        $test = new Test();
        $test->setExpiration($command->getTestModel()->getExpiration());
        $test->setModule($module);
        $test->setCreator($securityUser);

        $this->entityManager->persist($test);
    }
}
