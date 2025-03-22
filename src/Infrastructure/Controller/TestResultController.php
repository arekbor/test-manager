<?php 

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Entity\TestResult;
use App\Handler\FileHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/testResult')]
class TestResultController extends AbstractController
{
    #[Route('/download/{id}', name: 'app_testresult_download')]
    public function download(
        TestResult $testResult,
        FileHandler $fileHandler
    ): BinaryFileResponse
    {
        $file = $fileHandler->getFile($testResult, 'file');

        return $this->file($file);
    }
}