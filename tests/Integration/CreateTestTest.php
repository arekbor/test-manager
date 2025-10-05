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
use App\Domain\Entity\Test as TestEntity;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Test\Command\CreateTest\CreateTest;

final class CreateTestTest extends DatabaseTestCase
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
    public function testCreateTestCommandPersistsTestCorrectly(): void
    {
        //Arrange
        $testSecurityUser = new SecurityUser();
        $testSecurityUser->setPassword('secret');
        $testSecurityUser->setEmail('test_user@gmail.com');

        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');

        $this->entityManager->persist($testModule);
        $this->entityManager->persist($testSecurityUser);

        $this->entityManager->flush();

        $testModel = new TestModel();

        $expirationDate = date_modify(new \DateTime(), sprintf('+%d days', 12));
        $testModel->setExpiration($expirationDate);

        $command = new CreateTest($testModel, $testSecurityUser->getId(), $testModule->getId());

        //Act
        $this->commandBus->handle($command);

        /**
         * @var TestEntity $test
         */
        $test = $this->entityManager->getRepository(TestEntity::class)->findOneBy(['module' => $testModule->getId()]);

        //Assert
        $this->assertInstanceOf(TestEntity::class, $test);
        $this->assertNotNull($test->getId());
        $this->assertEquals($expirationDate, $test->getExpiration());
        $this->assertNull($test->getStart());
        $this->assertNull($test->getSubmission());
        $this->assertEquals($testModule->getId(), $test->getModule()->getId());
        $this->assertNull($test->getFirstname());
        $this->assertNull($test->getLastname());
        $this->assertNull($test->getEmail());
        $this->assertNull($test->getWorkplace());
        $this->assertNull($test->getDateOfBirth());
        $this->assertNull($test->getTestResult());
        $this->assertEquals('test_user@gmail.com', $test->getCreator()->getEmail());
        $this->assertNull($test->getScore());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testCreateTestCommandThrowsExceptionWhenModuleNotFound(): void
    {
        $notExistingModuleId = Uuid::v4();

        $command = new CreateTest(new TestModel(), Uuid::v4(), $notExistingModuleId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Module {"id":"%s"}', $notExistingModuleId->toString()));

        $this->commandBus->handle($command);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testCreateTestCommandThrowsExceptionWhenSecurityUserNotFound(): void
    {
        $testModule = new Module();
        $testModule->setName('Test module');
        $testModule->setLanguage('pl');
        $testModule->setCategory('introduction');

        $this->entityManager->persist($testModule);

        $this->entityManager->flush();

        $notExistingCreatorId = Uuid::v4();

        $command = new CreateTest(new TestModel(), $notExistingCreatorId, $testModule->getId());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\SecurityUser {"id":"%s"}', $notExistingCreatorId));

        $this->commandBus->handle($command);
    }
}
