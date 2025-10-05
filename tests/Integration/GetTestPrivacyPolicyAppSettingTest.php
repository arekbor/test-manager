<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\DatabaseTestCase;
use App\Domain\Entity\AppSetting;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Infrastructure\AppSetting\Service\AppSettingDecoder;
use App\Infrastructure\AppSetting\Service\AppSettingManager;
use App\Application\AppSetting\Model\TestPrivacyPolicyAppSetting;
use App\Application\Test\Query\GetTestPrivacyPolicyAppSetting\GetTestPrivacyPolicyAppSetting;

final class GetTestPrivacyPolicyAppSettingTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly QueryBusInterface $queryBus;
    private readonly AppSettingDecoder $appSettingDecoder;
    private readonly AppSettingManager $appSettingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->queryBus = $container->get(QueryBusInterface::class);
        $this->appSettingDecoder = $container->get(AppSettingDecoder::class);
        $this->appSettingManager = $container->get(AppSettingManager::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetTestPrivacyPolicyAppSettingQueryReturnsTestPrivacyPolicyAppSettingByProvidedLanguageCorrectly(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $testTestAppSetting = new TestAppSetting();
        $testTestAppSetting->addTestPrivacyPolicyAppSetting(
            (new TestPrivacyPolicyAppSetting())
                ->setContent('Privacy policy message')
                ->setLanguage('pl')
        );

        $encodedValue = $this->appSettingDecoder->decode($testTestAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestPrivacyPolicyAppSetting('pl');

        //Act
        /**
         * @var TestPrivacyPolicyAppSetting $testPrivacyPolicyAppSetting
         */
        $testPrivacyPolicyAppSetting = $this->queryBus->ask($query);

        //Assert
        $this->assertInstanceOf(TestPrivacyPolicyAppSetting::class, $testPrivacyPolicyAppSetting);

        $this->assertEquals('Privacy policy message', $testPrivacyPolicyAppSetting->getContent());
        $this->assertEquals('pl', $testPrivacyPolicyAppSetting->getLanguage());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetTestPrivacyPolicyAppSettingReturnsNullWhenTestPrivacyPolicyAppSettingByProvidedLanguageNotFound(): void
    {
        //Arrange
        $appSetting = new AppSetting();
        $appSetting->setKey(TestAppSetting::APP_SETTING_KEY);

        $testTestAppSetting = new TestAppSetting();
        $testTestAppSetting->addTestPrivacyPolicyAppSetting(
            (new TestPrivacyPolicyAppSetting())
                ->setContent('Privacy policy message')
                ->setLanguage('fr')
        );

        $encodedValue = $this->appSettingDecoder->decode($testTestAppSetting);
        $appSetting->setValue($encodedValue);

        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();

        $query = new GetTestPrivacyPolicyAppSetting('pl');

        //Act
        /**
         * @var TestPrivacyPolicyAppSetting|null $testPrivacyPolicyAppSetting
         */
        $testPrivacyPolicyAppSetting = $this->queryBus->ask($query);

        //Assert
        $this->assertNull($testPrivacyPolicyAppSetting);
    }
}
