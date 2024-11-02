<?php declare(strict_types=1);

namespace App\DataTable\Type;

use App\Entity\Question;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionDataTableType extends BaseDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('home', ButtonActionType::class, [
                'label' => 'data_table.question.home',
                'href' => $this->urlGenerator->generate('app_home_index')
            ])
            ->addAction('create', ButtonActionType::class, [
                'label' => 'data_table.question.create',
                'href' => $this->urlGenerator->generate('app_question_create', [ 
                    'moduleId' => $options['module_id'] 
                ])
            ]);
            
        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'data_table.actions',
                'actions' => [
                    'details' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'data_table.details',
                            'href' => function(Question $question) use($options): string {
                                return $this->urlGenerator->generate('app_question_details', [
                                    'moduleId' => $options['module_id'],
                                    'questionId' => $question->getId()
                                ]);
                            }
                        ]
                    ]
                ]
            ]);
        
        $builder
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'data_table.id'
            ])
            ->addColumn('content', TextColumnType::class, [
                'label' => 'data_table.question.content',
                'getter' => fn(Question $question) => $this->trimText($question->getContent())
            ])
            ->addColumn('answersCount', TextColumnType::class, [
                'label' => 'data_table.question.answersCount',
                'getter' => fn (Question $question) => count($question->getAnswers())
            ]);

        $builder
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'data_table.id'
            ])
            ->addFilter('content', StringFilterType::class, [
                'label' => 'data_table.question.content'
            ]);
        
        $builder
            ->setDefaultPaginationData(new PaginationData(page: 1, perPage: 5));

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('module_id');
    }
}
