<?php

declare(strict_types=1);

namespace App\Infrastructure\Form;

use App\Domain\Model\TestClauseAppSetting;
use App\Service\ParameterService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestClauseAppSettingType extends AbstractType
{
    public function __construct(
        private ParameterService $parameterService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'form.type.testClauseAppSetting.content.label',
                'empty_data' => '',
                'attr' => [
                    'rows' => 10
                ]
            ])
            ->add('language', ChoiceType::class, [
                'label' => false,
                'choices' => $this->parameterService->getAllowedLocales(),
                'empty_data' => $this->parameterService->getAllowedLocales()[0],
                'choice_label' => function($value) {
                    return $value;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TestClauseAppSetting::class,
        ]);
    }
}