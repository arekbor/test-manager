<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Form\FormInterface;
use App\Presentation\Form\UpdateVideoType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Application\Video\Model\UpdateVideoModel;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use App\Application\Video\Command\UpdateVideo\UpdateVideo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
final class UpdateVideoForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans,
    ) {}

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

            $this->commandBus->handle(new UpdateVideo($this->videoId, $updateVideoModel));
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
