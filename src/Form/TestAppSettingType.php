<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\TestAppSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestAppSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expirationDaysOffset', NumberType::class, [
                'label' => 'form.type.testAppSetting.expirationDaysOffset.label',
                'help' => 'form.type.testAppSetting.expirationDaysOffset.help',
            ])
            ->add('welcomeMessage', TextareaType::class, [
                'label' => 'form.type.testAppSetting.welcomeMessage.label',
                'help' => 'form.type.testAppSetting.welcomeMessage.help',
                'empty_data' => '',
                'attr' => [
                    'rows'=> 10
                ]
            ])
            ->add('farewellMessage', TextareaType::class, [
                'label' => 'form.type.testAppSetting.farewellMessage.label',
                'help' => 'form.type.testAppSetting.farewellMessage.help',
                'empty_data' => '',
                'attr' => [
                    'rows'=> 10
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TestAppSetting::class
        ]);
    }
}
