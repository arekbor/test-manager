<?php 

declare(strict_types=1);

namespace App\Presentation;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class TestManagerKernel extends BaseKernel
{
    use MicroKernelTrait;
}
