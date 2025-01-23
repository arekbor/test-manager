<?php 

declare(strict_types=1);

namespace App\Form;

use App\Model\TestSolve;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class TestSolveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'form.type.testSolve.firstname.label',
                'help' => 'form.type.testSolve.firstname.help',
                'empty_data' => ''
            ])
            ->add('lastname', TextType::class, [
                'label' => 'form.type.testSolve.lastname.label',
                'help' => 'form.type.testSolve.lastname.help',
                'empty_data' => ''
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.type.testSolve.email.label',
                'help' => 'form.type.testSolve.email.help',
                'empty_data' => ''
            ])
            ->add('workplace', TextType::class, [
                'label' => 'form.type.testSolve.workplace.label',
                'help' => 'form.type.testSolve.workplace.help',
                'empty_data' => ''
            ])
            ->add('dateOfBirth', DateType::class, array_merge(
                $options['test_category'] === 'periodic'
                    ? [
                        'label' => 'form.type.testSolve.dateOfBirth.label',
                        'help' => 'form.type.testSolve.dateOfBirth.help'
                    ] : [
                        'label' => false,
                        'attr' => ['class' => 'd-none'],
                        'required' => false,
                    ]
            ))
            ->add('testQuestions', LiveCollectionType::class, [
                'label' => false,
                'entry_type' => TestQuestionSolveType::class
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
        $resolver
            ->setDefaults([
                'data_class' => TestSolve::class,
            ])
            ->setRequired('test_category')
            ->setAllowedTypes('test_category', 'string')
        ;
    }
}