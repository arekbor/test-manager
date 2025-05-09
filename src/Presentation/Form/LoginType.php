<?php 

declare(strict_types=1);

namespace App\Presentation\Form;

use App\Domain\Entity\SecurityUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.type.login.email'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'form.password.label',
                'toggle' => true,
                'hidden_label' => 'form.password.hidden',
                'visible_label' => 'form.password.visible'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.type.login.submit',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-loading-button-target' => 'button'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecurityUser::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'csrf_token_id' => 'authenticate',
            'attr' => [
                'data-turbo' => 'false',
                'data-controller' => 'loading-button',
                'data-action' => 'submit->loading-button#startLoading',
            ]
        ]);
    }
}
