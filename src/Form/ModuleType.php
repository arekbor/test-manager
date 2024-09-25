<?php

namespace App\Form;

use App\Entity\Module;
use App\Service\LocaleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModuleType extends AbstractType
{
    public function __construct(private LocaleService $localeService) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'module.type.name.label',
                'empty_data' => '',
                'attr' => [
                    'data-loading' => 'addAttribute(disabled)',
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'module.type.language.label',
                'choices' => $this->localeService->getAllowedLocales(),
                'empty_data' => $this->localeService->getAllowedLocales()[0],
                'choice_label' => function($value) {
                    return $value;
                },
                'attr' => [
                    'data-loading' => 'addAttribute(disabled)',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'submit.label',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-loading' => 'addAttribute(disabled)',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'save',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
