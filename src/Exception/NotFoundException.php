<?php declare(strict_types=1);

namespace App\Exception;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $className, ?array $params = null) {
        $message = $className;
        
        if (!empty($params)) {
            $message .= ' ' . json_encode($params);
        }
        
        parent::__construct($message);
    }
}