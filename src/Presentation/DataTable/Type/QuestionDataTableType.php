<?php 

declare(strict_types = 1);

namespace App\Presentation\DataTable\Type;

use App\Application\Question\Model\QuestionViewModel;
use App\Presentation\DataTable\Column\Type\TruncatedTextColumnType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class QuestionDataTableType extends AbstractDataTableType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('create', ButtonActionType::class, [
                'label' => 'data_table.question.createQuestion',
                'href' => $this->urlGenerator->generate('app_question_create', [
                    'moduleId' => $options['module_id'] 
                ])
            ])
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'data_table.actions',
                'actions' => [
                    'details' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'data_table.details',
                            'href' => function(QuestionViewModel $questionViewModel): string {
                                return $this->urlGenerator->generate('app_question_details', [
                                    'moduleId' => $questionViewModel->getModuleId(),
                                    'questionId' => $questionViewModel->getId()
                                ]);
                            }
                        ]
                    ]
                ]
            ])
            ->addColumn('content', TruncatedTextColumnType::class, [
                'label' => 'data_table.question.content'
            ])
            ->addColumn('answersCount', TextColumnType::class, [
                'label' => 'data_table.question.answersCount'
            ])
            ->addFilter('content', StringFilterType::class, [
                'label' => 'data_table.question.content',
                'lower' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('module_id')
        ;
    }
}
