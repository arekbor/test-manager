<?php 

declare(strict_types=1);

namespace App\Attribute;

/**
 * This attribute checks if the test exists in the database and if it is active
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class TestVerify
{
    
}