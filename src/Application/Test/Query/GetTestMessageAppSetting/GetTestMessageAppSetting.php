<?php

declare(strict_types=1);

namespace App\Application\Test\Query\GetTestMessageAppSetting;

use App\Application\Shared\Bus\QueryInterface;

final class GetTestMessageAppSetting implements QueryInterface
{
    public function __construct(
        private readonly string $language
    ) {}

    public function getLanguage(): string
    {
        return $this->language;
    }
}
