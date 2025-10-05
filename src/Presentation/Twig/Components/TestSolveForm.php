<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\Shared\Bus\AsyncMessageBusInterface;
use Psr\Log\LoggerInterface;
use App\Presentation\Form\TestSolveType;
use App\Application\Test\Model\TestSolve;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Test\Model\DataForTestSolve;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Test\AsyncMessage\SendTestResultCsvToCreator;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use App\Application\Test\Command\SolveTest\SolveTest;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
final class TestSolveForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly AsyncMessageBusInterface $asyncMessage,
        private readonly LoggerInterface $logger,
    ) {}

    #[LiveProp]
    public ?\DateTimeInterface $start = null;

    #[LiveProp(useSerializerForHydration: true)]
    public DataForTestSolve $dataForTestSolve;

    #[PreMount]
    public function preMount(): void
    {
        $this->start = new \DateTimeImmutable();
    }

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $testId = $this->dataForTestSolve->getTestId();

        try {
            /**
             * @var TestSolve $testSolve
             */
            $testSolve = $this->getForm()->getData();

            $this->commandBus->handle(new SolveTest(
                testId: $testId,
                testSolve: $testSolve,
                start: $this->start,
                submission: new \DateTimeImmutable()
            ));
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $this->redirectToRoute('app_testsolve_notvalid');
        }

        $this->asyncMessage->send(new SendTestResultCsvToCreator($testId));

        return $this->redirectToRoute('app_testsolve_message', [
            'type' => 'conclusion',
            'id' => $testId
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestSolveType::class, $this->dataForTestSolve->getTestSolve(), [
            'test_category' => $this->dataForTestSolve->getTestCategory()
        ]);
    }
}
