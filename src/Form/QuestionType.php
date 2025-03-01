<?php 

declare(strict_types=1);

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'form.type.question.content_placeholder',
                    'rows' => 5,
                ]
            ])
            ->add('answers', LiveCollectionType::class, [
                'label' => false,
                'entry_type' => AnswerType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
                'button_add_options' => [
                    'label' => 'form.type.question.button.add',
                    'attr' => [
                        'class' => 'btn btn-primary',
                        'data-loading' => 'action(addCollectionItem)|addClass(loading)'
                    ]
                ],
                'button_delete_options' => [
                    'label' => 'form.type.question.button.delete',
                    'attr' => [
                        'class' => 'btn btn-danger',
                        'data-loading' => 'action(removeCollectionItem)|addAttribute(disabled)'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'debounce(300)|submit',
                    'data-loading' => 'action(submit)|addClass(loading)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'submit_label' => 'Submit',
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);
    }
}
