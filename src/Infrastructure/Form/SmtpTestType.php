<?php

declare(strict_types=1);

namespace App\Infrastructure\Form;

use App\Model\SmtpTest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmtpTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient', EmailType::class, [
                'label' => 'form.type.smtpTest.recipient.label',
                'help' => 'form.type.smtpTest.recipient.help',
            ])
            ->add('send', ButtonType::class, [
                'label' => 'form.type.smtpTest.send',
                'attr' => [
                    'class' => 'btn btn-warning',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'send',
                    'data-loading' => 'action(send)|addClass(loading)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SmtpTest::class
        ]);
    }
}
