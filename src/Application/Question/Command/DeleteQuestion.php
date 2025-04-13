<?php

declare(strict_types = 1);

namespace App\Application\Question\Command;

use Symfony\Component\Uid\Uuid;

final class DeleteQuestion
{
    private Uuid $questionId;

    public function __construct(
        Uuid $questionId
    ) {
        $this->questionId = $questionId;
    }

    public function getQuestionId(): Uuid
    {
        return $this->questionId;
    }
}