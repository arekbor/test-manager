<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\TestMessageAppSetting;
use App\Service\LocaleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestMessageAppSettingType extends AbstractType
{
    public function __construct(
        private LocaleService $localeService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('introduction', TextareaType::class, [
                'label' => 'form.type.testMessageAppSetting.introduction.label',
                'empty_data' => '',
                'attr' => [
                    'rows' => 10
                ]
            ])
            ->add('conclusion', TextareaType::class, [
                'label' => 'form.type.testMessageAppSetting.conclusion.label',
                'empty_data' => '',
                'attr' => [
                    'rows' => 10
                ]
            ])
            ->add('language', ChoiceType::class, [
                'label' => false,
                'choices' => $this->localeService->getAllowedLocales(),
                'empty_data' => $this->localeService->getAllowedLocales()[0],
                'choice_label' => function($value) {
                    return strtoupper($value);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TestMessageAppSetting::class,
        ]);
    }
}