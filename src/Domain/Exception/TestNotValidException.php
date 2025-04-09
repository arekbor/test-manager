<?php

declare(strict_types = 1);

namespace App\Domain\Exception;

use Symfony\Component\Uid\Uuid;

final class TestNotValidException extends \Exception
{
    public function __construct(Uuid $testId)
    {
        parent::__construct(sprintf("Test ID: %s is not valid.", $testId->toString()));
    }
}