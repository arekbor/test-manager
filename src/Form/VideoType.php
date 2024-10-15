<?php

namespace App\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class VideoType extends AbstractType
{
    private array $videoUploadExtensions;

    public function __construct(ParameterBagInterface $params) {
        $this->videoUploadExtensions = $params->get('app.video_upload_extensions');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', DropzoneType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'video.type.file.placeholder'
                ],
                'constraints' => [
                    new File([
                        'extensions' => $this->videoUploadExtensions,
                        'binaryFormat' => false,
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'video.type.submit.label',
                'attr' => [
                    'class' => 'btn btn-success',
                ]
            ])
        ;
    }
}
