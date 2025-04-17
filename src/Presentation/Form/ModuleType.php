<?php

declare(strict_types = 1);

namespace App\Presentation\Form;

use App\Application\Module\Model\ModuleModel;
use App\Application\Util\ParameterHelper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ModuleType extends AbstractType
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var array<string> $allowedLocales
         */
        $allowedLocales = ParameterHelper::explodeStringToArray($this->parameterBag->get('app.allowed_locales'));

        /**
         * @var array<string> $testCategory
         */
        $testCategory = ParameterHelper::explodeStringToArray($this->parameterBag->get('app.test_category'));

        $builder
            ->add('name', TextType::class, [
                'label' => 'form.type.module.name',
                'empty_data' => ''
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'form.type.module.language',
                'choices' => $allowedLocales,
                'empty_data' => $allowedLocales[0],
                'choice_label' => function($value) {
                    return $value;
                }
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'form.type.module.category',
                'choices' => $testCategory,
                'empty_data' => $testCategory[0],
                'choice_label' => function($value) {
                    return $value;
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-success',
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
                'data_class' => ModuleModel::class,
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
        ;
    }
}