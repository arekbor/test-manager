<?php 

declare(strict_types=1);

namespace App\Form;

use App\Model\MailSmtpAppSetting;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('host', TextType::class, [
                'label' => 'form.type.mailSmtpAppSetting.host.label',
                'help' => 'form.type.mailSmtpAppSetting.host.help',
                'empty_data' => ''
            ])
            ->add('port', TextType::class, [
                'label' => 'form.type.mailSmtpAppSetting.port.label',
                'help' => 'form.type.mailSmtpAppSetting.port.help',
                'empty_data' => ''
            ])
            ->add('fromAddress', EmailType::class, [
                'label' => 'form.type.mailSmtpAppSetting.fromAddress.label',
                'help' => 'form.type.mailSmtpAppSetting.fromAddress.help',
                'empty_data' => ''
            ])
            ->add('username', TextType::class, [
                'label' => 'form.type.mailSmtpAppSetting.username.label',
                'help' => 'form.type.mailSmtpAppSetting.username.help',
                'empty_data' => ''
            ])
            ->add('password', PasswordType::class, [
                'label' => 'form.password.label',
                'always_empty' => false,
                'toggle' => true,
                'hidden_label' => 'form.password.hidden',
                'visible_label' => 'form.password.visible',
                'empty_data' => '',
                'help' => 'form.type.mailSmtpAppSetting.password.help',
                'attr' => [
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add('smtpAuth', ChoiceType::class, [
                'label' => 'form.type.mailSmtpAppSetting.smtpAuth.label',
                'help' => 'form.type.mailSmtpAppSetting.smtpAuth.help',
                'choices' => [
                    'form.type.mailSmtpAppSetting.smtpAuth.enable' => true,
                    'form.type.mailSmtpAppSetting.smtpAuth.disable' => false
                ]
            ])
            ->add('smtpSecure', ChoiceType::class, [
                'label' => 'form.type.mailSmtpAppSetting.smtpSecure.label',
                'empty_data' => PHPMailer::ENCRYPTION_SMTPS,
                'help' => 'form.type.mailSmtpAppSetting.smtpSecure.help',
                'choices' => [
                    'form.type.mailSmtpAppSetting.smtpSecure.ssl' => PHPMailer::ENCRYPTION_SMTPS,
                    'form.type.mailSmtpAppSetting.smtpSecure.tls' => PHPMailer::ENCRYPTION_STARTTLS,
                ]
            ])
            ->add('timeout', NumberType::class, [
                'label' => 'form.type.mailSmtpAppSetting.timeout.label',
                'help' => 'form.type.mailSmtpAppSetting.timeout.help',
                'empty_data' => 0,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'debounce(300)|submit',
                    'data-loading' => 'action(submit)|addClass(loading)'
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
