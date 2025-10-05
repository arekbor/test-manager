<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\Video\Model\UpdateVideoModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Application\Video\Command\DeleteVideo\DeleteVideo;
use App\Application\Video\Query\GetVideoFile\GetVideoFile;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use App\Application\Video\Command\UploadVideoFile\UploadVideoFile;
use App\Application\Video\Query\GetUpdateVideoModel\GetUpdateVideoModel;

#[Route('/video')]
final class VideoController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[Route('/upload/{id}', name: 'app_video_upload')]
    public function upload(Uuid $id, Request $request): JsonResponse
    {
        try {
            /**
             * @var UploadedFile $uploadedFile
             */
            $uploadedFile = $request->files->get('file');

            $this->commandBus->handle(new UploadVideoFile($uploadedFile, $id));
        } catch (\Throwable $ex) {
            $errorMessage = $this->trans->trans('flash.videoController.upload.error');

            if ($ex instanceof HandlerFailedException && $ex->getPrevious() instanceof ValidatorException) {
                $errorMessage = $ex->getPrevious()->getMessage();
            }

            $this->addFlash('danger', $errorMessage);

            return $this->json($errorMessage, Response::HTTP_BAD_REQUEST);
        }

        $this->addFlash('success', $this->trans->trans('flash.videoController.upload.success'));

        return $this->json(null, Response::HTTP_OK);
    }

    #[Route('/download/{id}', name: 'app_video_download')]
    public function download(Uuid $id): BinaryFileResponse
    {
        /**
         * @var \SplFileInfo $file
         */
        $file = $this->queryBus->ask(new GetVideoFile($id));

        return $this->file($file);
    }

    #[Route('/delete/{moduleId}/{videoId}', name: 'app_video_delete')]
    public function delete(Uuid $moduleId, Uuid $videoId): Response
    {
        $response = $this->redirectToRoute('app_module_videos', [
            'id' => $moduleId
        ]);

        try {
            $this->commandBus->handle(new DeleteVideo($videoId));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.videoController.delete.error'));

            return $response;
        }

        $this->addFlash('success', $this->trans->trans('flash.videoController.delete.success'));

        return $response;
    }

    #[Route('/details/{moduleId}/{videoId}', name: 'app_video_details')]
    public function details(Uuid $moduleId, Uuid $videoId): Response
    {
        /**
         * @var UpdateVideoModel $updateVideoModel
         */
        $updateVideoModel = $this->queryBus->ask(new GetUpdateVideoModel($videoId));

        return $this->render('video/details.html.twig', [
            'updateVideoModel' => $updateVideoModel,
            'videoId' => $videoId,
            'moduleId' => $moduleId
        ]);
    }
}
