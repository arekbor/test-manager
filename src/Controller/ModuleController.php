<?php declare(strict_types=1);

namespace App\Controller;

use App\DataTable\Type\QuestionDataTableType;
use App\Entity\Module;
use App\Repository\QuestionRepository;
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
        QuestionRepository $questionRepository
    ): Response
    {
        $moduleId = $module->getId();
        $query = $questionRepository->findByModuleId($moduleId);
        $dataTable = $this->createDataTable(QuestionDataTableType::class, $query, [
            'module_id' => $moduleId
        ]);

        $dataTable->handleRequest($request);

        return $this->render('module/details.html.twig', [
            'module' => $module,
            'questions' => $dataTable->createView()
        ]);
    }
}
