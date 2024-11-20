<?php 

declare(strict_types=1);

namespace App\Controller;

use App\DataTable\Type\QuestionDataTableType;
use App\DataTable\Type\VideoDataTableType;
use App\Entity\Module;
use App\Repository\QuestionRepository;
use App\Repository\VideoRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/module')]
class ModuleController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/create')]
    public function create(): Response
    {
        return $this->render('module/create.html.twig');
    }

    #[Route('/details/general/{id}')]
    public function general(Module $module): Response
    {
        return $this->render('module/details.general.html.twig', [
            'module' => $module, 
        ]);
    }

    #[Route('/details/questions/{id}')]
    public function questions(
        Module $module, 
        Request $request,
        QuestionRepository $questionRepository
    ): Response
    {
        $moduleId = $module->getId();

        $questionsQuery = $questionRepository->findByModuleId($moduleId);
        $questionDataTable = $this->createDataTable(QuestionDataTableType::class, $questionsQuery, [
            'module_id' => $moduleId
        ]);
        
        $questionDataTable->handleRequest($request);

        return $this->render('module/details.questions.html.twig', [
            'module' => $module, 
            'question_data_table' => $questionDataTable->createView()
        ]);
    }

    #[Route('/details/videos/{id}')]
    public function videos(
        Module $module,
        Request $request,
        VideoRepository $videoRepository
    ): Response
    {
        $moduleId = $module->getId();

        $videosQuery = $videoRepository->findByModuleId($moduleId);
        $videoDataTable = $this->createDataTable(VideoDataTableType::class, $videosQuery, [
            'module_id' => $moduleId
        ]);

        $videoDataTable->handleRequest($request);

        return $this->render('module/details.videos.html.twig', [
            'module' => $module, 
            'video_data_table' => $videoDataTable->createView()
        ]);
    }
}
