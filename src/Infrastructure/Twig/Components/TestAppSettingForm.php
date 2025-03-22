<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig\Components;

use App\Exception\NotFoundException;
use App\Infrastructure\Form\TestAppSettingType;
use App\Model\TestAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
final class TestAppSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(useSerializerForHydration: true)]
    public ?TestAppSetting $testAppSetting = null;

    #[LiveAction]
    public function submit(
        AppSettingService $appSettingService,
        AppSettingRepository $appSettingRepository,
        EntityManagerInterface $em,
        TranslatorInterface $trans
    ): Response
    {
        $this->submitForm();

        $testAppSetting = $this->getForm()->getData();

        $appSetting = $appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new NotFoundException(TestAppSetting::class);
        }

        $appSetting = $appSettingService->updateValue($appSetting, $testAppSetting);

        $em->persist($appSetting);
        $em->flush();

        $this->addFlash('success', $trans->trans('flash.testAppSettingForm.successfullyUpdated'));

        return $this->redirectToRoute('app_settings_test');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestAppSettingType::class, $this->testAppSetting);
    }
}
