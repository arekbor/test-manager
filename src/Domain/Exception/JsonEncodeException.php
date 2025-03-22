<?php 

declare(strict_types=1);

namespace App\Domain\Exception;

class JsonEncodeException extends \Exception
{
    public function __construct(string $message = "Failed to encode data.") {
        parent::__construct($message);
    }
}