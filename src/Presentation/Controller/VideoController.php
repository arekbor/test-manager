<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Shared\QueryBusInterface;
use App\Application\Video\Command\DeleteVideo;
use App\Application\Video\Query\GetUpdateVideoModel;
use App\Application\Video\Model\UpdateVideoModel;
use App\Application\Video\Query\GetVideoFile;
use App\Domain\Entity\Module;
use App\Domain\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/video')]
class VideoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly QueryBusInterface $queryBus,
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {
    }

    #[Route('/upload/{id}', name: 'app_video_upload')]
    public function upload(
        Request $request,
        Module $module,
        TranslatorInterface $trans,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $video = new Video();

        $file = $request->files->get('file');
        $video->setFile($file);
        $video->addModule($module);

        $errors = $validator->validate($video);
        if (count($errors) > 0) {
            $error = $errors->get(0)->getMessage();

            $this->addFlash('danger', $error);
            
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($video);
        $this->em->flush();

        $this->addFlash('success', $trans->trans('flash.uploadFile.success'));
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
