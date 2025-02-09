<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Builder\TestSolveBuilder;
use App\Entity\Test;
use App\Entity\TestResult;
use App\Exception\NotFoundException;
use App\Form\TestSolveType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsLiveComponent]
final class TestSolveForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public Test $testProp;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $testSolve = $this
            ->getForm()
            ->getData()
        ;

        $this->testProp->setSubmission(new DateTime());
        $this->testProp->setFirstname($testSolve->getFirstname());
        $this->testProp->setLastname($testSolve->getLastname());
        $this->testProp->setEmail($testSolve->getEmail());
        $this->testProp->setWorkplace($testSolve->getWorkplace());
        $this->testProp->setDateOfBirth($testSolve->getDateOfBirth());

        $testResult = $this->uploadCsv($this->testProp);

        $this->em->persist($this->testProp);
        $this->em->persist($testResult);
        $this->em->flush();

        return $this->redirectToRoute('app_testsolve_conclusion');
    }

    protected function instantiateForm(): FormInterface
    {
        $testCategory = $this->testProp->getModule()->getCategory() 
            ?? throw new NotFoundException(string::class, ['testCategory']);

        $testSolveBuilder = new TestSolveBuilder();
        $testSolve = $testSolveBuilder->build($this->testProp);

        return $this->createForm(TestSolveType::class, $testSolve, [
            'test_category' => $testCategory
        ]);
    }

    private function uploadCsv(Test $test): TestResult
    {
        $testResult = new TestResult();
        
        $list = [
            ['email', $test->getEmail()],
            ['first name', $test->getFirstname()],
            ['last name', $test->getLastname()],
            ['work place', $test->getWorkplace()],
            ['submission', date_format($test->getSubmission(), "Y/m/d H:i:s")],
            ['questions count', count($test->getModule()->getQuestions())],
        ];
        
        // Tymczasowy plik
        $tempFilePath = sys_get_temp_dir() . '/test-result.csv';
        
        $fp = fopen($tempFilePath, 'w');
        
        foreach ($list as $line) {
            fputcsv($fp, $line, ',');
        }
        
        fclose($fp);

        $uploadedFile = new UploadedFile($tempFilePath, 'test-result.csv', 'text/csv', null, true);

        $testResult->setFile($uploadedFile);
        $testResult->setTest($test);

        return $testResult;
    }
}