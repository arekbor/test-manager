<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

final class HtmlFormatRuntime implements RuntimeExtensionInterface
{
    public function getHtmlFormatedText(?string $text = null): ?string
    {
        return $text ? nl2br(htmlspecialchars($text)) : null;
    }
}