<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use App\Domain\Entity\SecurityUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Test\Model\TestModel;
use App\Domain\Entity\Test as EntityTest;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Test\Command\UpdateTest\UpdateTest;

final class UpdateTestTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly CommandBusInterface $commandBus;
    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get(CommandBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateTestCommandSuccessfullyUpdatesTest(): void
    {
        //Arrange
        $testModule = new Module();
        $testModule->setName('Test Module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');

        $securityUser = new SecurityUser();
        $securityUser->setEmail('test@gmail.com');
        $securityUser->setPassword('secret');

        $entityTest = new EntityTest();

        $expirationDateBeforeUpdate = (new \DateTime())->modify('+5 days');
        $entityTest->setExpiration($expirationDateBeforeUpdate);
        $entityTest->setModule($testModule);
        $entityTest->setCreator($securityUser);

        $this->entityManager->persist($testModule);
        $this->entityManager->persist($securityUser);
        $this->entityManager->persist($entityTest);

        $this->entityManager->flush();

        $testModel = new TestModel();

        $expirationDateAfterUpdate = (new \DateTime())->modify('+32 days');
        $testModel->setExpiration($expirationDateAfterUpdate);

        $command = new UpdateTest($entityTest->getId(), $testModel);

        //Act
        $this->commandBus->handle($command);

        /**
         * @var EntityTest $test
         */
        $test = $this->entityManager->find(EntityTest::class, $entityTest->getId());

        //Assert
        $this->assertInstanceOf(EntityTest::class, $test);

        $this->assertEquals($test->getId(), $entityTest->getId());
        $this->assertEquals($test->getExpiration(), $expirationDateAfterUpdate);
        $this->assertEquals($test->getCreator(), $securityUser);
        $this->assertEquals($test->getModule(), $testModule);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateTestCommandThrowsExceptionWhenTestNotFound(): void
    {
        $notExistingTestId = Uuid::v4();

        $command = new UpdateTest($notExistingTestId, new TestModel());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Test {"id":"%s"}', $notExistingTestId->toString()));

        $this->commandBus->handle($command);
    }
}
