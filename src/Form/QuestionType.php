<?php

namespace App\Form;

use App\Entity\Question;
use Doctrine\DBAL\Types\SimpleArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'question.type.content.placeholder',
                    'rows' => 5,
                ],
                'constraints' => [
                    new NotBlank(),
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
                    'label' => 'question.type.button_add_options.label'
                ],
                'button_delete_options' => [
                    'label' => 'question.type.button_delete_options.label'
                ],
                'constraints' => [
                    new Count(min: 1)
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['submit_label'],
                'attr' => [
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'save'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'submit_label' => 'Submit'
        ]);
    }
}
