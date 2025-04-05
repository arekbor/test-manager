<?php

declare(strict_types = 1);

namespace App\Application\Question\Query;

use Symfony\Component\Uid\Uuid;

final class GetQuestionModel
{
    private Uuid $questionId;

    public function __construct(Uuid $questionId)
    {
        $this->questionId = $questionId;
    }

    public function getQuestionId(): Uuid
    {
        return $this->questionId;
    }
}