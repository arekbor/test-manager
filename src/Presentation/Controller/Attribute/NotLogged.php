<?php 

declare(strict_types=1);

namespace App\Presentation\Controller\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class NotLogged
{
    
}