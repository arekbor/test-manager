<?php

declare(strict_types = 1);

namespace App\Domain\Exception;

final class DateTimeModifyException extends \Exception
{
    public function __construct(string $message = 'Error while modifying date time')
    {
        parent::__construct($message);
    }
}