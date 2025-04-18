<?php

declare(strict_types = 1);

namespace App\Presentation\Form;

use App\Application\SecurityUser\Model\UpdateEmailModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.type.updateEmail.email.label',
                'help' => 'form.type.updateEmail.email.help',
                'help_attr' => [
                    'class' => 'text-warning'
                ]
            ])
            ->add('update', ButtonType::class, [
                'label' => 'form.type.updateEmail.update',
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
                'data_class' => UpdateEmailModel::class
            ])
        ;
    }
}
