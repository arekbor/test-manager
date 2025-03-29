<?php

declare(strict_types = 1);

namespace App\Infrastructure\Shared;

use App\Application\Shared\VichFileHandlerInterface;
use Vich\UploaderBundle\Exception\NoFileFoundException;
use Vich\UploaderBundle\Handler\AbstractHandler;

final class VichFileHandler extends AbstractHandler implements VichFileHandlerInterface
{
    public function handle(object|array $object, string $field, ?string $className = null): \SplFileInfo
    {
        $path = $this->storage->resolvePath($object, $field, $className);
        if (!$path) {
            throw new NoFileFoundException(sprintf('No file found in field "%s".', $field));
        }

        return new \SplFileInfo($path);
    }
}