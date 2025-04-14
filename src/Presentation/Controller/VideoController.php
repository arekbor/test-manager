<?php 

declare(strict_types = 1);

namespace App\Presentation\Controller;

use App\Application\Shared\QueryBusInterface;
use App\Application\Video\Command\DeleteVideo;
use App\Application\Video\Command\UploadVideoFile;
use App\Application\Video\Query\GetUpdateVideoModel;
use App\Application\Video\Model\UpdateVideoModel;
use App\Application\Video\Query\GetVideoFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\Exception\ValidatorException;

#[Route('/video')]
final class VideoController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {
    }

    #[Route('/upload/{id}', name: 'app_video_upload')]
    public function upload(Uuid $id, Request $request): JsonResponse
    {
        try {
            /**
             * @var UploadedFile $uploadedFile
             */
            $uploadedFile = $request->files->get('file');

            $this->commandBus->dispatch(new UploadVideoFile($uploadedFile, $id));
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
        $file = $this->queryBus->query(new GetVideoFile($id));

        return $this->file($file);
    }

    #[Route('/delete/{moduleId}/{videoId}', name: 'app_video_delete')]
    public function delete(Uuid $moduleId, Uuid $videoId): Response
    {
        $response = $this->redirectToRoute('app_module_videos', [
            'id' => $moduleId
        ]);

        try {
            $this->commandBus->dispatch(new DeleteVideo($videoId));
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
        $updateVideoModel = $this->queryBus->query(new GetUpdateVideoModel($videoId));

        return $this->render('video/details.html.twig', [
            'updateVideoModel' => $updateVideoModel,
            'videoId' => $videoId,
            'moduleId' => $moduleId
        ]);
    }
}
