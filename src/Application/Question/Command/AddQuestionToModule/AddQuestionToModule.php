<?php

declare(strict_types=1);

namespace App\Application\Question\Command\AddQuestionToModule;

use App\Application\Question\Model\QuestionModel;
use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class AddQuestionToModule implements CommandInterface
{
    public function __construct(
        private Uuid $moduleId,
        private QuestionModel $questionModel
    ) {}

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getQuestionModel(): QuestionModel
    {
        return $this->questionModel;
    }
}
