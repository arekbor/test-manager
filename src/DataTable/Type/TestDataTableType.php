<?php 

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Action\Type\ButtonGroupActionType;
use App\Entity\Test;
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
                    'more' => [
                        'type' => ButtonGroupActionType::class,
                        'type_options' => [
                            'buttons' => [
                                [
                                    'label' => 'data_table.details',
                                    'href' => function(Test $test): string {
                                        return $this->urlGenerator->generate('app_test_details', [
                                            'id' => $test->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.test.module',
                                    'href' => function(Test $test): string {
                                        return $this->urlGenerator->generate('app_module_general', [
                                            'id' => $test->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.test.test',
                                    'href' => function(Test $test): string {
                                        return $this->urlGenerator->generate('app_testsolve_solve', [
                                            '_locale' => $test->getModule()->getLanguage(),
                                            'id' => $test->getId()
                                        ]);
                                    }
                                ],
                            ]
                        ]
                    ]
                ]
            ])
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'data_table.id'
            ])
            ->addColumn('moduleId', NumberColumnType::class, [
                'label' => 'data_table.test.moduleId',
                'getter' => fn (Test $test) => $test->getModule()->getId()
            ])
            ->addColumn('moduleName', TextColumnType::class, [
                'label' => 'data_table.test.moduleName',
                'getter' => function (Test $test): string {
                    return $test->getModule()->getName();
                }
            ])
            ->addColumn('moduleLanguage', TextColumnType::class, [
                'label' => 'data_table.test.moduleLanguage',
                'getter' => function (Test $test): string {
                    return $this->trans->trans($test->getModule()->getLanguage());
                }
            ])
            ->addColumn('moduleTestCategory', TextColumnType::class, [
                'label' => 'data_table.test.moduleTestCategory',
                'getter' => function (Test $test): string {
                    return $this->trans->trans($test->getModule()->getCategory());
                }
            ])
            ->addColumn('email', TextColumnType::class, [
                'label' => 'data_table.test.email',
            ])
            ->addColumn('firstname', TextColumnType::class, [
                'label' => 'data_table.test.firstname'
            ])
            ->addColumn('lastname', TextColumnType::class, [
                'label' => 'data_table.test.lastname'
            ])
            ->addColumn('workplace', TextColumnType::class, [
                'label' => 'data_table.test.workplace'
            ])
            ->addColumn('dateOfBirth', DateTimeColumnType::class, [
                'label' => 'data_table.test.dateOfBirth'
            ])
            ->addColumn('expiration', DateTimeColumnType::class, [
                'label' => 'data_table.test.expiration'
            ])
            ->addColumn('submission', DateTimeColumnType::class, [
                'label' => 'data_table.test.submission'
            ])
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'data_table.id',
            ])
            ->addFilter('email', StringFilterType::class, [
                'label' => 'data_table.test.email',
                'lower' => true
            ])
            ->addFilter('firstname', StringFilterType::class, [
                'label' => 'data_table.test.firstname',
                'lower' => true
            ])
            ->addFilter('lastname', StringFilterType::class, [
                'label' => 'data_table.test.lastname',
                'lower' => true
            ])
            ->addFilter('workplace', StringFilterType::class, [
                'label' => 'data_table.test.workplace',
                'lower' => true
            ])
            ->addFilter('moduleId', NumericFilterType::class, [
                'label' => 'data_table.test.moduleId',
                'query_path' => 'module.id',
            ])
            ->addFilter('moduleName', StringFilterType::class, [
                'label' => 'data_table.test.moduleName',
                'query_path' => 'module.name',
                'lower' => true
            ])
        ;
    }
}