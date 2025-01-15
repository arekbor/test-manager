<?php 

declare(strict_types=1);

namespace App\Form;

use App\Model\TestSolve;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('dateOfBirth', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'form.type.testSolve.dateOfBirth.label',
                'help' => 'form.type.testSolve.dateOfBirth.help',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TestSolve::class,
        ]);
    }
}