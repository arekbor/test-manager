<?php

declare(strict_types = 1);

namespace App\Application\Question\Command;

use App\Application\Question\Model\QuestionModel;
use Symfony\Component\Uid\Uuid;

final class UpdateQuestion
{
    private Uuid $questionId;
    private Uuid $moduleId;
    private QuestionModel $questionModel;

    public function __construct(
        Uuid $questionId, 
        Uuid $moduleId,
        QuestionModel $questionModel
    ) {
        $this->questionId = $questionId;
        $this->moduleId = $moduleId;
        $this->questionModel = $questionModel;
    }

    public function getQuestionId(): Uuid
    {
        return $this->questionId;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getQuestionModel(): QuestionModel
    {
        return $this->questionModel;
    }
}