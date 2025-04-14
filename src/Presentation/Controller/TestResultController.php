<?php 

declare(strict_types = 1);

namespace App\Presentation\Controller;

use App\Application\Shared\QueryBusInterface;
use App\Application\Test\Query\GetTestResultFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/testResult')]
final class TestResultController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
    }

    #[Route('/download/{id}', name: 'app_testresult_download')]
    public function download(Uuid $id): BinaryFileResponse
    {
        /**
         * @var \SplFileInfo $file
         */
        $file = $this->queryBus->query(new GetTestResultFile($id));

        return $this->file($file);
    }
}