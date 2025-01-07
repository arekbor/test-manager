<?php 

declare(strict_types=1);

namespace App\Attribute;

/**
 * This attribute ignores language overrides in the user session.
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class IgnoreLocaleSession
{
    
}