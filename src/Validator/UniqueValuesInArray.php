<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UniqueValuesInArray extends Constraint
{
    public string $key;

    public function __construct(string $key, array $groups = null, mixed $payload = null) 
    {
        parent::__construct(options: ['key' => $key], groups: $groups, payload: $payload);

        $this->key = $key;
    }


    public function getRequiredOptions(): array
    {
        return ['key'];
    }
}
