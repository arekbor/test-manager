<?php

declare(strict_types=1);

namespace App\Presentation\DataTable\Type;

use App\Application\Module\Model\ModuleViewModel;
use App\Presentation\DataTable\Action\Type\ButtonGroupActionType;
use App\Presentation\DataTable\Column\Type\TruncatedTextColumnType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ModuleDataTableType extends AbstractDataTableType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $trans
    ) {}

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('create', ButtonActionType::class, [
                'label' => false,
                'icon' => 'plus',
                'attr' => [
                    'class' => 'btn btn-warning'
                ],
                'href' => $this->urlGenerator->generate('app_module_create'),
            ])
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'data_table.actions',
                'actions' => [
                    'more' => [
                        'type' => ButtonGroupActionType::class,
                        'type_options' => [
                            'buttons' => [
                                [
                                    'label' => 'data_table.details',
                                    'href' => function (ModuleViewModel $moduleViewModel): string {
                                        return $this->urlGenerator->generate('app_module_general', [
                                            'id' => $moduleViewModel->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.module.addQuestion',
                                    'href' => function (ModuleViewModel $moduleViewModel): string {
                                        return $this->urlGenerator->generate('app_question_create', [
                                            'moduleId' => $moduleViewModel->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.module.createTest',
                                    'href' => function (ModuleViewModel $moduleViewModel): string {
                                        return $this->urlGenerator->generate('app_test_create', [
                                            'id' => $moduleViewModel->getId()
                                        ]);
                                    }
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->addColumn('name', TruncatedTextColumnType::class, [
                'label' => 'data_table.module.name',
                'sort' => true
            ])
            ->addColumn('language', TextColumnType::class, [
                'label' => 'data_table.module.language',
                'getter' => function (ModuleViewModel $moduleViewModel): string {
                    return $this->trans->trans($moduleViewModel->getLanguage());
                },
                'sort' => true
            ])
            ->addColumn('category', TextColumnType::class, [
                'label' => 'data_table.module.category',
                'getter' => function (ModuleViewModel $moduleViewModel): string {
                    return $this->trans->trans($moduleViewModel->getCategory());
                },
                'sort' => true
            ])
            ->addColumn('questionsCount', NumberColumnType::class, [
                'label' => 'data_table.module.questionsCount',
            ])
            ->addColumn('videosCount', NumberColumnType::class, [
                'label' => 'data_table.module.videosCount',
            ])
            ->addFilter('name', StringFilterType::class, [
                'label' => 'data_table.module.name',
                'lower' => true,
            ])
        ;
    }
}
