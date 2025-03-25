<?php

declare(strict_types = 1);

namespace App\Application\Shared;

interface CsvGeneratorInterface
{
    public function generate(string $fileName, array $data): \SplFileInfo;
}