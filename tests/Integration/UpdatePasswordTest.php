<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\DatabaseTestCase;
use Symfony\Component\Uid\Uuid;
use App\Domain\Entity\SecurityUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\SecurityUser\Model\UpdatePasswordModel;
use App\Application\SecurityUser\Command\UpdatePassword\UpdatePassword;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdatePasswordTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly CommandBusInterface $commandBus;
    private readonly UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $container = $this->getContainer();

        $this->commandBus = $container->get(CommandBusInterface::class);
        $this->userPasswordHasher = $container->get(UserPasswordHasherInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdatePasswordCommandUpdatesPasswordSuccessfully(): void
    {
        //Arrange
        $password = '%NQw1lNJ[09-6(]=2R3mKLT$I';

        $testSecurityUser = new SecurityUser();

        $hashedPassowrd = $this->userPasswordHasher->hashPassword($testSecurityUser, $password);

        $testSecurityUser->setEmail('user@gmail.com');
        $testSecurityUser->setPassword($hashedPassowrd);

        $this->entityManager->persist($testSecurityUser);
        $this->entityManager->flush();

        $updatePasswordModel = new UpdatePasswordModel();
        $updatePasswordModel->setCurrentPassword($password);

        $newPassword = 'o!20a0|(Ap9P%wd1Bya5r)4;Aa8t&v{gK';

        $updatePasswordModel->setPassword($newPassword);

        $command = new UpdatePassword($testSecurityUser->getId(), $updatePasswordModel);

        //Act
        $this->commandBus->handle($command);

        /**
         * @var SecurityUser $securityUser
         */
        $securityUser = $this->entityManager->find(SecurityUser::class, $testSecurityUser->getId());

        $securityUserPassword = $securityUser->getPassword();

        //Assert
        $this->assertNotEquals($password, $securityUserPassword);
        $this->assertNotEquals($password, $newPassword);

        $this->assertTrue($this->userPasswordHasher->isPasswordValid($securityUser, $newPassword));
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdatePasswordCommandThrowsExceptionWhenCurrentPasswordIsInvalid(): void
    {
        $password = 'Fr3"Da1^w-2uh,VkKs4T@IqoXA';

        $securityUser = new SecurityUser();

        $hashedPassowrd = $this->userPasswordHasher->hashPassword($securityUser, $password);

        $securityUser->setEmail('test@gmail.com');
        $securityUser->setPassword($hashedPassowrd);

        $this->entityManager->persist($securityUser);
        $this->entityManager->flush();

        $updatePasswordModel = new UpdatePasswordModel();
        $updatePasswordModel->setCurrentPassword('^Lyb18}7$GcTK||(£Ku');

        $command = new UpdatePassword($securityUser->getId(), $updatePasswordModel);

        $this->expectExceptionMessage('Invalid current password.');

        $this->commandBus->handle($command);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testUpdatePasswordCommandThrowsExceptionWhenUserNotFound(): void
    {
        $notExistingUserId = Uuid::v4();

        $command = new UpdatePassword($notExistingUserId, new UpdatePasswordModel());

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\SecurityUser {"id":"%s"}', $notExistingUserId));

        $this->commandBus->handle($command);
    }
}
