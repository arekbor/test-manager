<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Shared\Bus\QueryBusInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\Test\Query\GetTestResultFile\GetTestResultFile;

#[Route('/testResult')]
final class TestResultController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {}

    #[Route('/download/{id}', name: 'app_testresult_download')]
    public function download(Uuid $id): BinaryFileResponse
    {
        /**
         * @var \SplFileInfo $file
         */
        $file = $this->queryBus->ask(new GetTestResultFile($id));

        return $this->file($file);
    }
}
