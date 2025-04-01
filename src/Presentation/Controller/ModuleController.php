<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Module\Model\ModuleModel;
use App\Application\Module\Query\GetModuleModel;
use App\Application\Shared\QueryBusInterface;
use App\Domain\Entity\Module;
use App\Presentation\DataTable\Type\ModuleDataTableType;
use App\Presentation\DataTable\Type\QuestionDataTableType;
use App\Presentation\DataTable\Type\VideoDataTableType;
use App\Repository\ModuleRepository;
use App\Repository\QuestionRepository;
use App\Repository\VideoRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/module')]
class ModuleController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    public function __construct(
        private readonly QueryBusInterface $queryBus
    ) {
    }

    #[Route('/create', name: 'app_module_create')]
    public function create(): Response
    {
        return $this->render('module/create.html.twig');
    }

    #[Route('/details/general/{id}', name: 'app_module_general')]
    public function general(Uuid $id): Response
    {
        /**
         * @var ModuleModel $moduleModel
         */
        $moduleModel = $this->queryBus->query(new GetModuleModel($id));

        return $this->render('module/general.html.twig', [
            'moduleId' => $id,
            'moduleModel' => $moduleModel, 
        ]);
    }

    #[Route('/details/questions/{id}', name: 'app_module_questions')]
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

        return $this->render('module/questions.html.twig', [
            'moduleId' => $module->getId(),
            'question_data_table' => $questionDataTable->createView()
        ]);
    }

    #[Route('/details/videos/{id}', name: 'app_module_videos')]
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

        return $this->render('module/videos.html.twig', [
            'moduleId' => $module->getId(),
            'video_data_table' => $videoDataTable->createView()
        ]);
    }

    #[Route('/index', name: 'app_module_index')]
    public function index(
        Request $request,
        ModuleRepository $moduleRepository
    ): Response
    {
        $query = $moduleRepository->createQueryBuilder('m');
        $moduleDataTable = $this->createDataTable(ModuleDataTableType::class, $query); 
        $moduleDataTable->handleRequest($request);

        return $this->render('module/index.html.twig', [
            'module_data_table' => $moduleDataTable->createView()
        ]);
    }
}
