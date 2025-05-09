<?php

declare(strict_types = 1);

namespace App\Presentation\Form;

use App\Application\AppSetting\Model\TestMessageAppSetting;
use App\Application\Util\ParameterHelper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TestMessageAppSettingType extends AbstractType
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var array<string> $allowedLocales
         */
        $allowedLocales = ParameterHelper::explodeStringToArray($this->parameterBag->get('app.allowed_locales'));

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
                'choices' => $allowedLocales,
                'empty_data' => $allowedLocales[0],
                'choice_label' => function($value) {
                    return $value;
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