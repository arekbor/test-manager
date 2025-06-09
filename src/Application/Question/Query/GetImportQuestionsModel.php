<?php

declare(strict_types=1);

namespace App\Application\Question\Query;

final class GetImportQuestionsModel
{
    private \SplFileInfo $csv;

    public function __construct(\SplFileInfo $csv)
    {
        $this->csv = $csv;
    }

    public function getCsv(): \SplFileInfo
    {
        return $this->csv;
    }
}
