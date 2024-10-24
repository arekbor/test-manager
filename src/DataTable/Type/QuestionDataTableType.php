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
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class QuestionDataTableType extends AbstractDataTableType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('home', ButtonActionType::class, [
                'label' => 'question.data.table.home.label',
                'href' => $this->urlGenerator->generate('app_home_index')
            ])
            ->addAction('create', ButtonActionType::class, [
                'label' => 'question.data.table.create.label',
                'href' => $this->urlGenerator->generate('app_question_create', [ 
                    'moduleId' => $options['module_id'] 
                ])
            ]);
            
        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'question.data.table.actions.label',
                'actions' => [
                    'details' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'question.data.table.details.label',
                            'href' => function(Question $question) use($options): string {
                                return $this->urlGenerator->generate('app_question_edit', [
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
                'label' => 'question.data.table.id.label'
            ])
            ->addColumn('content', TextColumnType::class, [
                'label' => 'question.data.table.content.label'
            ])
            ->addColumn('answersCount', TextColumnType::class, [
                'label' => 'question.data.table.answersCount.label',
                'getter' => fn (Question $question) => count($question->getAnswers())
            ]);

        $builder
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'question.data.table.id.label'
            ])
            ->addFilter('content', StringFilterType::class, [
                'label' => 'question.data.table.content.label'
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
