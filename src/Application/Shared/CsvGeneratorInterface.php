<?php

declare(strict_types=1);

namespace App\Application\Shared;

interface CsvGeneratorInterface
{
    /**
     * @param array<mixed, mixed> $data
     */
    public function generate(string $fileName, array $data): \SplFileInfo;
}
