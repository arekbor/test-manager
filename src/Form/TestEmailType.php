<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\TestEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receiver', EmailType::class, [
                'label' => 'form.type.testEmail.receiver.label',
                'help' => 'form.type.testEmail.receiver.help',
            ])
            ->add('send', ButtonType::class, [
                'label' => 'form.type.testEmail.send',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'send',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TestEmail::class
        ]);
    }
}
