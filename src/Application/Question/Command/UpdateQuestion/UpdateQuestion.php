<?php

declare(strict_types=1);

namespace App\Application\Question\Command\UpdateQuestion;

use App\Application\Question\Model\QuestionModel;
use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateQuestion implements CommandInterface
{
    public function __construct(
        private readonly Uuid $questionId,
        private readonly Uuid $moduleId,
        private readonly QuestionModel $questionModel
    ) {}

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
