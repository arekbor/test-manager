<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use App\Domain\Entity\SecurityUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Domain\Entity\Test as EntityTest;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Test\Command\DeleteTest\DeleteTest;

final class DeleteTestTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testDeleteTestCommandDeletesTestSuccessfully(): void
    {
        //Arrange
        $testCreator = new SecurityUser();
        $testCreator->setEmail('test@gmail.com');
        $testCreator->setPassword('secret');

        $entityTest = new EntityTest();
        $entityTest->setCreator($testCreator);

        $this->entityManager->persist($testCreator);
        $this->entityManager->persist($entityTest);
        $this->entityManager->flush();

        $entityTestId = $entityTest->getId();

        $comamnd = new DeleteTest($entityTestId);

        //Act
        $this->commandBus->handle($comamnd);

        /**
         * @var EntityTest|null $test
         */
        $test = $this->entityManager->find(EntityTest::class, $entityTestId);

        //Assert
        $this->assertNull($test);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testDeleteTestCommandThrowsExceptionWhenTestNotFound(): void
    {
        $notExistingTestId = Uuid::v4();

        $command = new DeleteTest($notExistingTestId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Test {"id":"%s"}', $notExistingTestId->toString()));

        $this->commandBus->handle($command);
    }
}
