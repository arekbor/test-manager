<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Shared\VichFileHandlerInterface;
use App\Domain\Entity\TestResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/testResult')]
class TestResultController extends AbstractController
{
    #[Route('/download/{id}', name: 'app_testresult_download')]
    public function download(
        TestResult $testResult,
        VichFileHandlerInterface $vichFileHandler
    ): BinaryFileResponse
    {
        $file = $vichFileHandler->handle($testResult, TestResult::FILE_FIELD_NAME);

        return $this->file($file);
    }
}