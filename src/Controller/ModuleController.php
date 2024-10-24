<?php declare(strict_types=1);

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

    #[Route('/details/{id}')]
    public function details(
        Module $module, 
        Request $request, 
        QuestionRepository $questionRepository,
        VideoRepository $videoRepository
    ): Response
    {
        $moduleId = $module->getId();

        $questionsQuery = $questionRepository->findByModuleId($moduleId);
        $questionsDataTable = $this->createDataTable(QuestionDataTableType::class, $questionsQuery, [
            'module_id' => $moduleId
        ]);
        $questionsDataTable->handleRequest($request);

        $videosQuery = $videoRepository->findByModuleId($moduleId);
        $videosDataTable = $this->createDataTable(VideoDataTableType::class, $videosQuery, [
            'module_id' => $moduleId
        ]);
        $videosDataTable->handleRequest($request);

        return $this->render('module/details.html.twig', [
            'module' => $module, 
            'questions' => $questionsDataTable->createView(),
            'videos' => $videosDataTable->createView()
        ]);
    }
}
