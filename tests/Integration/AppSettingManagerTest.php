<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\AppSetting\DTO\AppSettingToCreate;
use App\Domain\Entity\AppSetting;
use App\Domain\Model\MailSmtpAppSetting;
use App\Domain\Model\TestAppSetting;
use App\Infrastructure\AppSetting\Repository\AppSettingRepository;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Infrastructure\Shared\UnitOfWork;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class AppSettingManagerTest extends DatabaseTestCase
{
    private readonly AppSettingManager $appSettingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $appSettingDecoder = new AppSettingDecoder($serializer);

        $appSettingRepository = new AppSettingRepository($this->entityManager);
        $unitOfWork = new UnitOfWork($this->entityManager);

        $this->appSettingManager = new AppSettingManager($appSettingDecoder, $appSettingRepository, $unitOfWork);
    }

    #[Group("Integration")]
    public function testCreateManyPersistsAppSettingsCorrectly(): void
    {
        //arrange
        $appSettings = [
            new AppSettingToCreate(MailSmtpAppSetting::APP_SETTING_KEY, new MailSmtpAppSetting()),
            new AppSettingToCreate(TestAppSetting::APP_SETTING_KEY, new TestAppSetting())
        ];

        //act
        $this->appSettingManager->createMany(...$appSettings);

        $repo = $this->entityManager->getRepository(AppSetting::class);

        $mailSmtpAppSetting = $repo->findOneBy(['key' => MailSmtpAppSetting::APP_SETTING_KEY]);
        $testAppSetting = $repo->findOneBy(['key' => TestAppSetting::APP_SETTING_KEY]);

        //assert
        $this->assertNotNull($mailSmtpAppSetting);
        $this->assertNotNull($testAppSetting);
    }
}