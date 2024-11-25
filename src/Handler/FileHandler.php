<?php

declare(strict_types=1);

namespace App\Handler;

use SplFileInfo;
use Vich\UploaderBundle\Exception\NoFileFoundException;
use Vich\UploaderBundle\Handler\AbstractHandler;

final class FileHandler extends AbstractHandler
{
    public function getFile(object|array $object, string $field, ?string $className = null): SplFileInfo
    {
        $path = $this->storage->resolvePath($object, $field, $className);

        if ($path === null) {
            throw new NoFileFoundException(sprintf('No file found in field "%s".', $field));
        }

        return new SplFileInfo($path);
    }
}