<?php 

declare(strict_types=1);

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
    public function __construct(
        private LocaleService $localeService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.type.module.name',
                'empty_data' => ''
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'form.type.module.language',
                'choices' => $this->localeService->getAllowedLocales(),
                'empty_data' => $this->localeService->getAllowedLocales()[0],
                'choice_label' => function($value) {
                    return strtoupper($value);
                },
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
            'data_class' => Module::class,
        ]);
    }
}
