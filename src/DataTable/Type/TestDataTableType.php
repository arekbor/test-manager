<?php 

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Action\Type\CopyToClipboardType;
use App\Entity\Test;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TestDataTableType extends AbstractDataTableType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $trans,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'data_table.actions',
                'actions' => [
                    'details' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'data_table.details',
                            'href' => function(Test $test): string {
                                return $this->urlGenerator->generate('app_test_details', [
                                    'id' => $test->getId()
                                ]);
                            }
                        ]
                    ],
                    'testSolve' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'data_table.test.testSolve',
                            'href' => function(Test $test): string {
                                return $this->urlGenerator->generate('app_test_solve', [
                                    'id' => $test->getId()
                                ]);
                            }
                        ]
                    ],
                    'testSolveCopyLink' => [
                        'type' => CopyToClipboardType::class,
                        'type_options' => [
                            'label' => 'data_table.test.testSolveCopyLink',
                            'clipboard_link' => function(Test $test): string {
                                return $this->urlGenerator->generate('app_test_solve', [
                                    'id' => $test->getId()
                                ], UrlGeneratorInterface::ABSOLUTE_URL);
                            }
                        ]
                    ],
                ]
            ])
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'data_table.id'
            ])
            ->addColumn('moduleId', NumberColumnType::class, [
                'label' => 'data_table.test.moduleId',
                'getter' => fn (Test $test) => $test->getModule()->getId()
            ])
            ->addColumn('takerEmail', TextColumnType::class, [
                'label' => 'data_table.test.takerEmail'
            ])
            ->addColumn('expiration', DateTimeColumnType::class, [
                'label' => 'data_table.test.expiration'
            ])
            ->addColumn('submission', DateTimeColumnType::class, [
                'label' => 'data_table.test.submission'
            ])
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'data_table.id'
            ])
            ->addFilter('takerEmail', StringFilterType::class, [
                'label' => 'data_table.test.takerEmail'
            ])
            ->addFilter('moduleId', NumericFilterType::class, [
                'label' => 'data_table.test.moduleId',
                'query_path' => 'module.id',
            ])
        ;
    }
}