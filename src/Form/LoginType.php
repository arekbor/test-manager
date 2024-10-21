<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\SecurityUser;
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
                'label' => 'form.login.email.label'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'form.login.password.label',
                'toggle' => true,
                'hidden_label' => 'form.login.password.hidden.password.label',
                'visible_label' => 'form.login.password.visible.password.label'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.login.submit.label',
                'attr' => [
                    'class' => 'btn btn-success'
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
        ]);
    }
}
