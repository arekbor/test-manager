<?php

declare(strict_types = 1);

namespace App\Presentation\Form;

use App\Application\Test\Model\TestModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expiration', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'form.type.test.expiration.label',
                'help' => 'form.type.test.expiration.help',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'submit',
                    'data-loading' => 'action(submit)|addClass(loading)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => TestModel::class
            ])
        ;
    } 
}
