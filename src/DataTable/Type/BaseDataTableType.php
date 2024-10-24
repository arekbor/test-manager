<?php declare(strict_types=1);

namespace App\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class BaseDataTableType extends AbstractDataTableType
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator
    ) {
    }
}