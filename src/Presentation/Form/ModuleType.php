<?php 

declare(strict_types=1);

namespace App\Presentation\Form;

use App\Domain\Entity\Module;
use App\Service\ParameterService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleType extends AbstractType
{
    public function __construct(
        private ParameterService $parameterService
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
                'choices' => $this->parameterService->getAllowedLocales(),
                'empty_data' => $this->parameterService->getAllowedLocales()[0],
                'choice_label' => function($value) {
                    return $value;
                },
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'form.type.module.category',
                'choices' => $this->parameterService->getTestCategory(),
                'empty_data' => $this->parameterService->getTestCategory()[0],
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
        $resolver->setDefaults([
            'data_class' => Module::class,
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);
    }
}
