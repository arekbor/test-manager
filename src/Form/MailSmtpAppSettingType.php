<?php declare(strict_types=1);

namespace App\Form;

use App\Model\MailSmtpAppSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailSmtpAppSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serverAddress', TextType::class, [
                'label' => 'form.type.mailSmtpAppSetting.serverAddress',
                'empty_data' => ''
            ])
            ->add('serverPort', TextType::class, [
                'label' => 'form.type.mailSmtpAppSetting.serverPort',
                'empty_data' => ''
            ])
            ->add('fromAddress', EmailType::class, [
                'label' => 'form.type.mailSmtpAppSetting.fromAddress',
                'empty_data' => ''
            ])
            ->add('name', TextType::class, [
                'label' => 'form.type.mailSmtpAppSetting.name',
                'empty_data' => ''
            ])
            ->add('password', PasswordType::class, [
                'label' => 'form.password.label',
                'always_empty' => false,
                'toggle' => true,
                'hidden_label' => 'form.password.hidden',
                'visible_label' => 'form.password.visible',
                'empty_data' => '',
                'attr' => [
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'submit',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MailSmtpAppSetting::class
        ]);
    }
}
