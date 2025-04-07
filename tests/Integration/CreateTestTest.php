<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Test\Command\CreateTest;
use App\Application\Test\Model\TestModel;
use App\Domain\Entity\Module;
use App\Domain\Entity\SecurityUser;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Domain\Entity\Test as TestEntity;
use Symfony\Component\Uid\Uuid;

final class CreateTestTest extends DatabaseTestCase
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

        $command = new CreateTest($testModel, 'test_user@gmail.com', $testModule->getId());

        //Act
        $this->commandBus->dispatch($command);

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

        $command = new CreateTest(new TestModel(), 'test@gmail.com', $notExistingModuleId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Module {"id":"%s"}', $notExistingModuleId->toString()));

        $this->commandBus->dispatch($command);
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

        $command = new CreateTest(new TestModel(), 'test@gmail.com', $testModule->getId());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\SecurityUser {"email":"%s"}', 'test@gmail.com'));

        $this->commandBus->dispatch($command);
    }
}