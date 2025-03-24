<?php 

declare(strict_types=1);

namespace App\Domain\Exception;

final class TestResultNotificationDisabledException extends \Exception
{
    public function __construct(string $message = "Test result notification is disabled") {
        parent::__construct($message);
    }
}