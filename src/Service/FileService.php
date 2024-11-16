<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\File;

class FileService
{
    public function save(UploadedFile $file, string $path, string $filename): File
    {
        $extension = $this->getExtension($file);
        $filename = $this->getFilename($filename, $extension);
        $fullPath = $this->combinePath($path, $filename);

        if (file_exists($fullPath)) {
            throw new Exception("File already exists.");
        }

        $file->move($path, $filename);

        return new File($fullPath);
    }

    public function delete(string $path, string $filename): bool
    {
        $fullPath = $this->combinePath($path, $filename);

        return unlink($fullPath);
    }

    public function getFile(string $path, string $filename): File
    {
        $fullPath = $this->combinePath($path, $filename);

        return new File($fullPath);
    }

    private function getExtension(UploadedFile $file): string
    {
        $extension = $file->guessExtension();
        if (empty($extension)) {
            throw new Exception("Could not guess extension file.");
        }

        return $extension;
    }

    private function getFilename(string $filename, string $extension): string
    {
        return $filename . '.' . $extension;
    }

    private function combinePath(string $path, string $filename): string
    {
        return $path . DIRECTORY_SEPARATOR . $filename;
    }
}