<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\SecurityUser\Command\UpdateSecurityUserEmail;
use App\Application\SecurityUser\Model\UpdateEmail;
use App\Domain\Entity\SecurityUser;
use App\Tests\DatabaseTestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateSecurityUserEmailTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->getContainer()->get('command.bus');
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

        $updateEmail = new UpdateEmail();
        $updateEmail->setEmail('new_email@gmail.com');

        $command = new UpdateSecurityUserEmail($testSecurityUser->getId(), $updateEmail);

        //Act
        $this->commandBus->dispatch($command);

        /**
         * @var SecurityUser $securityUser
         */
        $securityUser = $this->entityManager->find(SecurityUser::class, $testSecurityUser->getId());

        //Assert
        $this->assertEquals($testSecurityUser->getId(), $securityUser->getId());
        $this->assertEquals($updateEmail->getEmail(), $securityUser->getEmail());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateSecurityUserEmailCommandThrowsWhenUpdateEmailIsTheSameeAsTheCurrentEmail(): void
    {
        $testSecurityUser = new SecurityUser();
        $testSecurityUser->setEmail('test_user@gmail.com');
        $testSecurityUser->setPassword('secret');

        $this->entityManager->persist($testSecurityUser);
        $this->entityManager->flush();

        $updateEmail = new UpdateEmail();
        $updateEmail->setEmail('test_user@gmail.com');

        $command = new UpdateSecurityUserEmail($testSecurityUser->getId(), $updateEmail);

        $this->expectExceptionMessage("Cannot update email: the provided email address is the same as the current one.");

        $this->commandBus->dispatch($command);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateSecurityUserEmailCommandThrowsWhenUpdateEmailAlreadyExists(): void
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

        $updateEmail = new UpdateEmail();
        $updateEmail->setEmail('admin@gmail.com');

        $command = new UpdateSecurityUserEmail($testSecurityUser->getId(), $updateEmail);

        $this->expectException(UniqueConstraintViolationException::class);

        $this->commandBus->dispatch($command);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdateSecurityUserEmailCommandThrowsWhenSecurityUserNotFound(): void
    {
        $notExistingSecurityUserId = Uuid::v4();

        $command = new UpdateSecurityUserEmail($notExistingSecurityUserId, new UpdateEmail());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\SecurityUser {"id":"%s"}', $notExistingSecurityUserId));

        $this->commandBus->dispatch($command);
    }
}