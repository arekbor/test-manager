<?php

declare(strict_types=1);

namespace App\Presentation\DataTable\Type;

use App\Application\Test\Model\TestViewModel;
use App\Presentation\DataTable\Action\Type\ButtonGroupActionType;
use App\Presentation\DataTable\Column\Type\TruncatedTextColumnType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TestDataTableType extends AbstractDataTableType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $trans,
    ) {}

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
                                    'href' => function (TestViewModel $testViewModel): string {
                                        return $this->urlGenerator->generate('app_test_details', [
                                            'id' => $testViewModel->getId(),
                                            'moduleId' => $testViewModel->getModuleId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.test.module',
                                    'href' => function (TestViewModel $testViewModel): string {
                                        return $this->urlGenerator->generate('app_module_general', [
                                            'id' => $testViewModel->getModuleId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.test.test',
                                    'visible' => function (TestViewModel $testViewModel): bool {
                                        $now = new \DateTime();
                                        return $now < $testViewModel->getExpiration() && $testViewModel->getSubmission() === null;
                                    },
                                    'href' => function (TestViewModel $testViewModel): string {
                                        return $this->urlGenerator->generate('app_testsolve_message', [
                                            '_locale' => $testViewModel->getModuleLanguage(),
                                            'type' => 'introduction',
                                            'id' => $testViewModel->getId()
                                        ]);
                                    },
                                    'copyToClipboard' => true
                                ],
                                [
                                    'label' => 'data_table.test.testResult',
                                    'visible' => function (TestViewModel $testViewModel): bool {
                                        return $testViewModel->getTestResultId() !== null;
                                    },
                                    'href' => function (TestViewModel $testViewModel): ?string {
                                        $testResultId = $testViewModel->getTestResultId();
                                        if ($testResultId) {
                                            return $this->urlGenerator->generate('app_testresult_download', [
                                                'id' => $testResultId
                                            ]);
                                        }

                                        return null;
                                    },
                                    'attr' => [
                                        'data-turbo' => 'false'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->addColumn('moduleName', TruncatedTextColumnType::class, [
                'label' => 'data_table.test.moduleName'
            ])
            ->addColumn('moduleLanguage', TextColumnType::class, [
                'label' => 'data_table.test.moduleLanguage',
                'getter' => function (TestViewModel $testViewModel): string {
                    return $this->trans->trans($testViewModel->getModuleLanguage());
                }
            ])
            ->addColumn('moduleTestCategory', TextColumnType::class, [
                'label' => 'data_table.test.moduleTestCategory',
                'getter' => function (TestViewModel $testViewModel): string {
                    return $this->trans->trans($testViewModel->getModuleCategory());
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
                'label' => 'data_table.test.expiration',
                'getter' => function (TestViewModel $testViewModel) {
                    return $testViewModel->getExpiration();
                },
                'value_attr' => function (\DateTime $expiration): array {
                    $now = new \DateTimeImmutable();

                    if ($expiration < $now) {
                        return ['class' => 'text-danger'];
                    }

                    return ['class' => 'text-success'];
                }
            ])
            ->addColumn('start', DateTimeColumnType::class, [
                'label' => 'data_table.test.start'
            ])
            ->addColumn('submission', DateTimeColumnType::class, [
                'label' => 'data_table.test.submission'
            ])
            ->addColumn('score', NumberColumnType::class, [
                'label' => 'data_table.test.score'
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
            ->addFilter('score', NumericFilterType::class, [
                'label' => 'data_table.test.score'
            ])
        ;
    }
}
