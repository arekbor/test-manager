<?php

declare(strict_types = 1);

namespace App\Presentation\Twig\Components;

use App\Application\AppSetting\Command\UpdateTestAppSetting;
use App\Presentation\Form\TestAppSettingType;
use App\Application\AppSetting\Model\TestAppSetting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
final class UpdateTestAppSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {
    }

    #[LiveProp(useSerializerForHydration: true)]
    public TestAppSetting $testAppSetting;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        try {
            /**
             * @var TestAppSetting $testAppSetting
             */
            $testAppSetting = $this->getForm()->getData();

            $this->commandBus->dispatch(new UpdateTestAppSetting($testAppSetting));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateTestAppSettingForm.error'));

            return $this->redirectToRoute('app_settings_test');
        }

        $this->addFlash('success', $this->trans->trans('flash.updateTestAppSettingForm.success'));

        return $this->redirectToRoute('app_settings_test');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestAppSettingType::class, $this->testAppSetting);
    }
}
