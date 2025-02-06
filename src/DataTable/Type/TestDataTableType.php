<?php 

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Action\Type\ButtonGroupActionType;
use App\DataTable\Column\Type\TruncatedTextColumnType;
use App\Entity\Test;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
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
                                            'id' => $test->getModule()->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.test.test',
                                    'href' => function(Test $test): string {
                                        return $this->urlGenerator->generate('app_testsolve_introduction', [
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
            ->addColumn('moduleName', TruncatedTextColumnType::class, [
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
            ->addColumn('dateOfBirth', DateColumnType::class, [
                'label' => 'data_table.test.dateOfBirth'
            ])
            ->addColumn('expiration', DateTimeColumnType::class, [
                'label' => 'data_table.test.expiration'
            ])
            ->addColumn('submission', DateTimeColumnType::class, [
                'label' => 'data_table.test.submission'
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
            ->addFilter('moduleName', StringFilterType::class, [
                'label' => 'data_table.test.moduleName',
                'query_path' => 'module.name',
                'lower' => true
            ])
        ;
    }
}