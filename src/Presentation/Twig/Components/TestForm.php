<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Domain\Entity\Module;
use App\Domain\Entity\SecurityUser;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use App\Presentation\Form\TestType;
use App\Domain\Model\TestAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class TestForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public Module $moduleProp;

    #[LiveProp]
    public ?Test $testProp = null;

    public function __construct(
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService,
    ) {
    }
    
    #[LiveAction]
    public function submit(
        EntityManagerInterface $em,
        Security $security,
        TranslatorInterface $trans
    ): Response
    {
        $this->submitForm();

        $test = $this->getForm()->getData();

        $test->setModule($this->moduleProp);

        $creator = $security->getUser();
        if ($creator instanceof SecurityUser) {
            $test->setCreator($creator);
        }

        $em->persist($test);
        $em->flush();

        $this->addFlash('success', $trans->trans('flash.testForm.successfullyCreated'));

        return $this->redirectToRoute('app_test_index');
    }

    protected function instantiateForm(): FormInterface
    {
        if (!$this->testProp) {
            $this->testProp = new Test();
            $this->testProp->setExpiration($this->getDefaultExpirationDate());
        }

        return $this->createForm(TestType::class, $this->testProp);
    }

    private function getDefaultExpirationDate(): \DateTime
    {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        if (!$appSetting) {
            throw new NotFoundException(TestAppSetting::class);
        }

        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);
        $daysOffset = $testAppSetting->getExpirationDaysOffset();

        return (new \DateTime())->modify(sprintf('+%d days', $daysOffset));
    }
}
