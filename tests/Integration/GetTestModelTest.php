<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Module;
use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use App\Domain\Entity\SecurityUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Domain\Entity\Test as EntityTest;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\Test\Model\TestModel;
use App\Application\Test\Query\GetTestModel\GetTestModel;

final class GetTestModelTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly QueryBusInterface $queryBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryBus = self::getContainer()->get(QueryBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetTestModelQueryReturnsTestModelCorrectly(): void
    {
        //Arrange
        $testSecurityUser = new SecurityUser();
        $testSecurityUser->setEmail('test_user@gmail.com');
        $testSecurityUser->setPassword('secret');

        $testModule = new Module();
        $testModule->setName('Test Module');
        $testModule->setLanguage('en');
        $testModule->setCategory('introduction');

        $entityTest = new EntityTest();
        $expirationDate = (new \DateTime())->modify('+4 days');

        $entityTest->setModule($testModule);

        $entityTest->setExpiration($expirationDate);
        $entityTest->setCreator($testSecurityUser);

        $this->entityManager->persist($testModule);
        $this->entityManager->persist($testSecurityUser);
        $this->entityManager->persist($entityTest);

        $this->entityManager->flush();

        $query = new GetTestModel($entityTest->getId());

        //Act
        /**
         * @var TestModel $testModel
         */
        $testModel = $this->queryBus->ask($query);

        //Assert
        $this->assertInstanceOf(TestModel::class, $testModel);
        $this->assertEquals($expirationDate, $testModel->getExpiration());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetTestModelQueryThrowsExceptionWhenTestNotFound(): void
    {
        $notExistingTestId = Uuid::v4();

        $query = new GetTestModel($notExistingTestId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Test {"id":"%s"}', $notExistingTestId->toString()));

        $this->queryBus->ask($query);
    }
}
