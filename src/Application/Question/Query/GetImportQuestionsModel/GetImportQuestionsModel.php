<?php

declare(strict_types=1);

namespace App\Application\Question\Query\GetImportQuestionsModel;

use App\Application\Shared\Bus\QueryInterface;

final class GetImportQuestionsModel implements QueryInterface
{
    public function __construct(private readonly \SplFileInfo $csv) {}

    public function getCsv(): \SplFileInfo
    {
        return $this->csv;
    }
}
