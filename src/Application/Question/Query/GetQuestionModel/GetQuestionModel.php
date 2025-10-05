<?php

declare(strict_types=1);

namespace App\Application\Question\Query\GetQuestionModel;

use App\Application\Shared\Bus\QueryInterface;
use Symfony\Component\Uid\Uuid;

final class GetQuestionModel implements QueryInterface
{
    public function __construct(private readonly Uuid $questionId) {}

    public function getQuestionId(): Uuid
    {
        return $this->questionId;
    }
}
