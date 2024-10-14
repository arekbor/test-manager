<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class VideoType extends AbstractType
{
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
                        'maxSize' => '10M',
                        'binaryFormat' => false,
                        'extensions' => [ 'mp4', 'mov' ]
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
