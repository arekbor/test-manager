<?php

namespace App\Form;

use App\Entity\Test;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('takerEmail', EmailType::class, [
                'label' => 'form.type.createTest.takerEmail.label',
                'help' => 'form.type.createTest.takerEmail.help',
            ])
            ->add('expiration', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => (new DateTime())->modify('+7 days'),
                'label' => 'form.type.createTest.expiration.label',
                'help' => 'form.type.createTest.expiration.help',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'submit',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    } 
}
