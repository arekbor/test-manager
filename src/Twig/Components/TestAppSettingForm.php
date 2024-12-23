<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Exception\NotFoundException;
use App\Form\TestAppSettingType;
use App\Model\TestAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class TestAppSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public TestAppSetting $testAppSetting;

    #[LiveAction]
    public function submit(
        AppSettingService $appSettingService,
        AppSettingRepository $appSettingRepository,
        EntityManagerInterface $em,
    ): Response
    {
        $this->submitForm();

        $testAppSetting = $this
            ->getForm()
            ->getData()
        ;

        $appSetting = $appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new NotFoundException(TestAppSetting::class);
        }

        $appSetting = $appSettingService->updateValue($appSetting, $testAppSetting);

        $em->persist($appSetting);
        $em->flush();

        return $this->redirectToRoute('app_settings_test');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestAppSettingType::class, $this->testAppSetting);
    }
}