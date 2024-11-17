<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LocaleService 
{
    public function __construct(
        private ParameterBagInterface $params
    ) { 
    }

    public function getAllowedLocales(): array
    {
        $allowedLocales = $this->params->get('app.allowed_locales');

        return explode("|", $allowedLocales);
    }
}