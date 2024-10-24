<?php declare(strict_types=1);

namespace App\Controller;

use App\DataTable\Type\QuestionDataTableType;
use App\DataTable\Type\VideoDataTableType;
use App\Entity\Module;
use App\Repository\QuestionRepository;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/module')]
class ModuleController extends BaseDataTableController
{
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
        $videosQuery = $videoRepository->findByModuleId($moduleId);

        return $this->render('module/details.html.twig', [
            'module' => $module, 
            'question_data_table_view' => $this->createDataTableView(QuestionDataTableType::class, $request, $questionsQuery, [
                'module_id' => $moduleId
            ]),
            'video_data_table_view' => $this->createDataTableView(VideoDataTableType::class, $request, $videosQuery, [
                'module_id' => $moduleId
            ])
        ]);
    }
}
