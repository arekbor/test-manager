<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\IgnoreLocaleSession;
use App\Attribute\TestVerify;
use App\DataTable\Type\TestDataTableType;
use App\Entity\Module;
use App\Entity\Test;
use App\Entity\Video;
use App\Handler\FileHandler;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/index')]
    public function index(
        Request $request, 
        TestRepository $testRepository
    ): Response
    {
        $query = $testRepository->findAllWithModules();
        $testDataTable = $this->createDataTable(TestDataTableType::class, $query);
        $testDataTable->handleRequest($request);

        return $this->render('test/index.html.twig', [
            'test_data_table' => $testDataTable->createView()
        ]);
    }

    #[Route('/create/{id}')]
    public function create(Module $module): Response
    {
        return $this->render('test/create.html.twig', [
            'module' => $module
        ]);
    }

    #[Route('/details/{id}')]
    public function details(Test $test): Response
    {
        return $this->render('test/details.html.twig', [
            'test' => $test
        ]);
    }

    #[Route('/delete/{id}')]
    public function delete(Test $test, EntityManagerInterface $em): Response
    {
        $em->remove($test);
        $em->flush();

        return $this->redirectToRoute('app_test_index');
    }

    #[Route('/notFound')]
    #[IgnoreLocaleSession]
    public function notFound(): Response 
    {
        return $this->render('/test/solve.notFound.html.twig');
    }

    #[Route('/notValid')]
    #[IgnoreLocaleSession]
    public function notValid(): Response 
    {
        return $this->render('/test/solve.notValid.html.twig');
    }

    #[Route('/video/{testId}/{videoId}')]
    public function video(
        #[MapEntity(id: 'testId')] Test $test,
        #[MapEntity(id: 'videoId')] Video $video,
        FileHandler $fileHandler,
    ): Response
    {
        if (!$test->videoBelongsToTest($video)) {
            return $this->redirectToRoute('app_test_notvalid');
        }   

        $file = $fileHandler->getFile($video, 'videoFile');
        return $this->file($file);
    }

    #[Route('/introduction/{id}')]
    #[IgnoreLocaleSession]
    #[TestVerify]
    public function introduction(?Test $test): Response
    {
        return $this->render('/test/solve.introduction.html.twig', [
            'test' => $test
        ]);
    }

    #[Route('/conclusion/{id}')]
    #[IgnoreLocaleSession]
    #[TestVerify]
    public function conclusion(?Test $test): Response
    {
        return $this->render('/test/solve.conclusion.html.twig', [
            'test' => $test
        ]);
    }
}