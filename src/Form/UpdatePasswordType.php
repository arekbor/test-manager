<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\UpdatePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'form.type.updatePassword.currentPassword.label',
                'toggle' => true,
                'hidden_label' => 'form.password.hidden',
                'visible_label' => 'form.password.visible',
                'always_empty' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'form.type.updatePassword.password_first_options.label',
                    'help' => 'form.type.updatePassword.password_first_options.help',
                    'help_attr' => [
                        'class' => 'text-warning'
                    ],
                    'always_empty' => false,
                ],
                'second_options' => [
                    'label' => 'form.type.updatePassword.password_second_options',
                    'always_empty' => false,
                ],
            ])
            ->add('update', ButtonType::class, [
                'label' => 'form.type.updatePassword.update',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'debounce|update',
                    'data-loading' => 'action(update)|addClass(loading)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdatePassword::class
        ]);
    }
}
