<?php

declare(strict_types = 1);

namespace App\Presentation\Form;

use App\Application\Video\Model\UpdateVideoModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateVideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('originalName', TextType::class, [
                'label' => 'form.type.updateVideo.originalName.label',
                'help' => 'form.type.updateVideo.originalName.help'
            ])
            ->add('update', ButtonType::class, [
                'label' => 'form.type.updateVideo.update',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'update',
                    'data-loading' => 'action(update)|addClass(loading)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => UpdateVideoModel::class
            ])
        ;
    }
}