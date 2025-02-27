<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Video;
use App\Form\UpdateVideoType;
use App\Model\UpdateVideo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
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

    #[LiveProp]
    public Video $videoProp;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleIdProp;

    #[LiveAction]
    public function update(
        EntityManagerInterface $em,
        TranslatorInterface $trans
    ): Response
    {
        $this->submitForm();

        $updateVideo = $this->getForm()->getData();

        $newVideoName = $updateVideo->getOriginalName();
        $this->videoProp->setOriginalName($newVideoName);
        $em->persist($this->videoProp);
        $em->flush();

        $this->addFlash('success', $trans->trans('flash.updateVideoForm.successfullyUpdated'));

        return $this->redirectToRoute('app_video_details', [
            'moduleId' => $this->moduleIdProp,
            'videoId' => $this->videoProp->getId()
        ]);
    }

    protected function instantiateForm(): FormInterface 
    {
        $updateVideo = new UpdateVideo();
        $originalName = $this->videoProp->getOriginalName();
        $updateVideo->setOriginalName($originalName);

        return $this->createForm(UpdateVideoType::class, $updateVideo);
    }
}