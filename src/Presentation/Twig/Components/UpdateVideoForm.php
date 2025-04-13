<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\Video\Command\UpdateVideo;
use App\Application\Video\Model\UpdateVideoModel;
use App\Presentation\Form\UpdateVideoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsLiveComponent]
final class UpdateVideoForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {
    }

    #[LiveProp]
    public UpdateVideoModel $updateVideoModel;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $videoId;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    #[LiveAction]
    public function update(): Response
    {
        $this->submitForm();

        try {
            /**
             * @var UpdateVideoModel $updateVideoModel
             */
            $updateVideoModel = $this->getForm()->getData();

            $this->commandBus->dispatch(new UpdateVideo($this->videoId, $updateVideoModel));
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.updateVideoForm.error'));

            return $this->redirectToRoute('app_module_index');
        }

        $this->addFlash('success', $this->trans->trans('flash.updateVideoForm.success'));

        return $this->redirectToRoute('app_video_details', [
            'moduleId' => $this->moduleId,
            'videoId' => $this->videoId
        ]);
    }

    protected function instantiateForm(): FormInterface 
    {
        return $this->createForm(UpdateVideoType::class, $this->updateVideoModel);
    }
}