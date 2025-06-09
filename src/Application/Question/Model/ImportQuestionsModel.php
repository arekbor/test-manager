<?php

declare(strict_types=1);

namespace App\Application\Question\Model;

use App\Application\Question\Model\QuestionModel;

final class ImportQuestionsModel
{
    private array $questionModels = [];

    public function addQuestionModel(QuestionModel $questionModel): static
    {
        if (!in_array($questionModel, $this->questionModels, true)) {
            $this->questionModels[] = $questionModel;
        }

        return $this;
    }

    public function getQuestionModels(): array
    {
        return $this->questionModels;
    }
}
