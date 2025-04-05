<?php 

declare(strict_types = 1);

namespace App\Presentation\Form;

use App\Application\Question\Model\QuestionModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class QuestionType extends AbstractType
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
            ->add('answerModels', LiveCollectionType::class, [
                'entry_type' => AnswerType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
                'button_add_options' => [
                    'label' => 'form.type.question.button.add',
                    'attr' => [
                        'class' => 'btn btn-primary',
                        'data-live-action-param' => 'addCollectionItem',
                        'data-loading' => 'action(addCollectionItem)|addClass(loading)'
                    ]
                ],
                'button_delete_options' => [
                    'label' => 'form.type.question.button.delete',
                    'attr' => [
                        'class' => 'btn btn-danger',
                        'data-live-action-param' => 'removeCollectionItem',
                        'data-loading' => 'action(removeCollectionItem)|addAttribute(disabled)'
                    ]
                ]
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
                'data_class' => QuestionModel::class
            ])
        ;
    }
}
