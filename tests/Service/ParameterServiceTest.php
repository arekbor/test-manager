<?php 

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ParameterService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ParameterServiceTest extends TestCase
{
    private ParameterService $parameterService;

    protected function setUp(): void
    {
        $params = new ParameterBag([
            "app.allowed_locales" => "en|pl|fr|ua",
            "app.test_category" => "introductory|periodic",
        ]);

        $this->parameterService = new ParameterService($params);
    }

    public function testGetAllowedLocales(): void
    {
        $locales = $this->parameterService->getAllowedLocales();

        $this->assertEquals(["en", "pl", "fr", "ua"], $locales);
    }

    public function testGetTestCategory(): void
    {
        $locales = $this->parameterService->getTestCategory();

        $this->assertEquals(["introductory", "periodic"], $locales);
    }
}