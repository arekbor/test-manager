<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetTestPrivacyPolicyAppSetting;

use App\Application\Shared\Bus\QueryInterface;

final class GetTestPrivacyPolicyAppSetting implements QueryInterface
{
    public function __construct(
        private readonly string $language
    ) {}

    public function getLanguage(): string
    {
        return $this->language;
    }
}
