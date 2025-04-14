<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Test\Command\DeleteTest;
use App\Domain\Entity\SecurityUser;
use App\Domain\Entity\Test as EntityTest;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class DeleteTestTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('command.bus');
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
        $this->commandBus->dispatch($comamnd);

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

        $this->commandBus->dispatch($command);
    }
}