<?php declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\AppSetting;
use App\Service\AppSettingService;
use App\Tests\Model\TestSetting;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AppSettingServiceTest extends TestCase
{
    private AppSettingService $appSettingService;

    protected function setUp(): void
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $this->appSettingService = new AppSettingService($serializer);
    }

    public function testSetValue(): void
    {
        $data = new TestSetting();
        $data->setText($testText = "test text");
        $data->setNumber($testNumber = 332);

        $appSetting = $this
            ->appSettingService
            ->setValue($key = "test.key", $data)
        ;

        $this->assertEquals($appSetting->getKey(), $key);

        $this->assertEquals($appSetting->getValue(), [
            "text" => $testText,
            "number" => $testNumber
        ]);
    }

    public function testGetValue(): void
    {
        $appSetting = new AppSetting();
        $appSetting->setKey("test.key");
        $appSetting->setValue([
            "text" => $testText = "some text",
            "number" => $testNumber = 512
        ]);

        $testSetting = $this
            ->appSettingService
            ->getValue($appSetting, TestSetting::class)
        ;

        $this->assertEquals($testSetting->getText(), $testText);
        $this->assertEquals($testSetting->getNumber(), $testNumber);
    }

    public function testUpdateValue(): void
    {   
        $appSetting = new AppSetting();

        $appSetting->setKey("test.key");
        $appSetting->setValue([
            "text" => $testText = "text",
            "number" => $testNumber = 12
        ]);

        $appSetting = $this
            ->appSettingService
            ->updateValue($appSetting, [
                "text" => "some updated text",
                "number" => 500,
            ])
        ;

        $updatedTestSetting = $this
            ->appSettingService
            ->getValue($appSetting, TestSetting::class)
        ;

        $this->assertNotEquals($updatedTestSetting->getText(), $testText);
        $this->assertNotEquals($updatedTestSetting->getNumber(), $testNumber);
    }
}