<?php 

declare(strict_types=1);

namespace App\Exception;

use Exception;

class JsonDecodeException extends Exception
{
    public function __construct(string $message = "Failed to decode data.") {
        parent::__construct($message);
    }
}