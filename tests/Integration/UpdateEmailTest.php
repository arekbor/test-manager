<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use App\Domain\Entity\SecurityUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\SecurityUser\Model\UpdateEmailModel;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Application\SecurityUser\Command\UpdateEmail\UpdateEmail;

final class UpdateEmailTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->getContainer()->get(CommandBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateSecurityUserEmailCommandSuccessfullyUpdatesEmail(): void
    {
        //Arrange
        $testSecurityUser = new SecurityUser();
        $testSecurityUser->setEmail('test_user@gmail.com');
        $testSecurityUser->setPassword('secret');

        $this->entityManager->persist($testSecurityUser);
        $this->entityManager->flush();

        $updateEmailModel = new UpdateEmailModel();
        $updateEmailModel->setEmail('new_email@gmail.com');

        $command = new UpdateEmail($testSecurityUser->getId(), $updateEmailModel);

        //Act
        $this->commandBus->handle($command);

        /**
         * @var SecurityUser $securityUser
         */
        $securityUser = $this->entityManager->find(SecurityUser::class, $testSecurityUser->getId());

        //Assert
        $this->assertEquals($testSecurityUser->getId(), $securityUser->getId());
        $this->assertEquals($updateEmailModel->getEmail(), $securityUser->getEmail());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateSecurityUserEmailCommandThrowsExceptionWhenUpdateEmailIsTheSameeAsTheCurrentEmail(): void
    {
        $testSecurityUser = new SecurityUser();
        $testSecurityUser->setEmail('test_user@gmail.com');
        $testSecurityUser->setPassword('secret');

        $this->entityManager->persist($testSecurityUser);
        $this->entityManager->flush();

        $updateEmailModel = new UpdateEmailModel();
        $updateEmailModel->setEmail('test_user@gmail.com');

        $command = new UpdateEmail($testSecurityUser->getId(), $updateEmailModel);

        $this->expectExceptionMessage("Cannot update email: the provided email address is the same as the current one.");

        $this->commandBus->handle($command);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateSecurityUserEmailCommandThrowsExceptionWhenUpdateEmailAlreadyExists(): void
    {
        $testSecurityUser = new SecurityUser();
        $testSecurityUser->setEmail('test_user@gmail.com');
        $testSecurityUser->setPassword('secret');

        $existingSecurityUser = new SecurityUser();
        $existingSecurityUser->setEmail('admin@gmail.com');
        $existingSecurityUser->setPassword('secret');

        $this->entityManager->persist($testSecurityUser);
        $this->entityManager->persist($existingSecurityUser);
        $this->entityManager->flush();

        $updateEmailModel = new UpdateEmailModel();
        $updateEmailModel->setEmail('admin@gmail.com');

        $command = new UpdateEmail($testSecurityUser->getId(), $updateEmailModel);

        $this->expectException(UniqueConstraintViolationException::class);

        $this->commandBus->handle($command);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateSecurityUserEmailCommandThrowsExceptionWhenSecurityUserNotFound(): void
    {
        $notExistingSecurityUserId = Uuid::v4();

        $command = new UpdateEmail($notExistingSecurityUserId, new UpdateEmailModel());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\SecurityUser {"id":"%s"}', $notExistingSecurityUserId));

        $this->commandBus->handle($command);
    }
}
