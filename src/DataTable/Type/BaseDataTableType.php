<?php declare(strict_types=1);

namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class BaseDataTableType extends AbstractDataTableType
{
    private const MAX_LENGHT_TEXT = 20;

    public function __construct(
        protected UrlGeneratorInterface $urlGenerator
    ) {
    }

    protected function trimText(string $string): string
    {
        return strlen($string) > BaseDataTableType::MAX_LENGHT_TEXT ? 
            substr($string, 0, BaseDataTableType::MAX_LENGHT_TEXT) . "..." : 
            $string;
    }
}