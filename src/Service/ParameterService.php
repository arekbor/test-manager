<?php 

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ParameterService 
{
    public function __construct(
        private ParameterBagInterface $params
    ) { 
    }

    public function getAllowedLocales(): array
    {
        return $this->resolveParameter('app.allowed_locales');
    }

    public function getTestCategory(): array
    {
        return $this->resolveParameter('app.test_category');
    }

    private function resolveParameter(string $param): array
    {
        $containerParam = $this->params->get($param);

        return explode("|", $containerParam);
    }
}