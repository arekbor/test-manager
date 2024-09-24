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

class ModuleType extends AbstractType
{
    public function __construct(private LocaleService $localeService) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'module.type.name.label'
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'module.type.language.label',
                'choices' => $this->localeService->getAllowedLocales(),
                'choice_label' => function($value) {
                    return $value;
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'submit.label',
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
