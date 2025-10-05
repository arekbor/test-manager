<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\AppSetting\Command\UpdateTestAppSetting\UpdateTestAppSetting;
use App\Presentation\Form\TestAppSettingType;
use App\Application\AppSetting\Model\TestAppSetting;
use App\Application\Shared\Bus\CommandBusInterface;
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
final class UpdateTestAppSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {}

    #[LiveProp(useSerializerForHydration: true)]
    public TestAppSetting $testAppSetting;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $redirect = $this->redirectToRoute('app_settings_test');

        try {
            /**
             * @var TestAppSetting $testAppSetting
             */
            $testAppSetting = $this->getForm()->getData();

            $this->commandBus->handle(new UpdateTestAppSetting($testAppSetting));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateTestAppSettingForm.error'));

            return $redirect;
        }

        $this->addFlash('success', $this->trans->trans('flash.updateTestAppSettingForm.success'));

        return $redirect;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestAppSettingType::class, $this->testAppSetting);
    }
}
