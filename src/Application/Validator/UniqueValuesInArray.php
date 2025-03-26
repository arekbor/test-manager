<?php

declare(strict_types = 1);

namespace App\Application\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class UniqueValuesInArray extends Constraint
{
    public string $key;

    public function __construct(string $key, ?array $groups = null, mixed $payload = null) 
    {
        parent::__construct(options: ['key' => $key], groups: $groups, payload: $payload);

        $this->key = $key;
    }

    public function getRequiredOptions(): array
    {
        return ['key'];
    }
}
