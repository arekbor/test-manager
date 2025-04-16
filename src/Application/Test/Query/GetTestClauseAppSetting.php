<?php

declare(strict_types = 1);

namespace App\Application\Test\Query;

final class GetTestClauseAppSetting
{
    private string $language;

    public function __construct(
        string $language
    ) {
        $this->language = $language;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}