<?php 

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\LocaleService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class LocaleServiceTest extends TestCase
{
    private LocaleService $localeService;

    protected function setUp(): void
    {
        $params = new ParameterBag([
            "app.allowed_locales" => "en|pl|fr|ua"
        ]);

        $this->localeService = new LocaleService($params);
    }

    public function testGetAllowedLocales(): void
    {
        $locales = $this->localeService->getAllowedLocales();

        $this->assertEquals(["en", "pl", "fr", "ua"], $locales);
    }
}