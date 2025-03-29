<?php 

declare(strict_types=1);

namespace App\Presentation\Form;

use App\Application\Test\Model\TestQuestionSolve;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class TestQuestionSolveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('testAnswers', LiveCollectionType::class, [
                'label' => false,
                'entry_type' => TestAnswerSolveType::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => TestQuestionSolve::class
            ])
        ;
    }
}