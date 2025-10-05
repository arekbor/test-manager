<?php

declare(strict_types=1);

namespace App\Application\Question\Command\DeleteQuestion;

use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class DeleteQuestion implements CommandInterface
{
    public function __construct(
        private readonly Uuid $questionId
    ) {}

    public function getQuestionId(): Uuid
    {
        return $this->questionId;
    }
}
