<?php 

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Action\Type\ButtonGroupActionType;
use App\DataTable\Column\Type\TruncatedTextColumnType;
use App\Entity\Module;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ModuleDataTableType extends AbstractDataTableType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('create', ButtonActionType::class, [
                'label' => 'data_table.module.create',
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
                                    'href' => function(Module $module): string {
                                        return $this->urlGenerator->generate('app_module_general', [
                                            'id' => $module->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.module.addQuestion',
                                    'href' => function(Module $module): string {
                                        return $this->urlGenerator->generate('app_question_create', [
                                            'moduleId' => $module->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.module.createTest',
                                    'href' => function(Module $module): string {
                                        return $this->urlGenerator->generate('app_test_create', [
                                            'id' => $module->getId()
                                        ]);
                                    }
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'data_table.id'
            ])
            ->addColumn('name', TruncatedTextColumnType::class, [
                'label' => 'data_table.module.name',
                'getter' => fn(Module $module) => $module->getName()
            ])
            ->addColumn('language', TextColumnType::class, [ 
                'label' => 'data_table.module.language',
                'getter' => fn (Module $module) => strtoupper($module->getLanguage())
            ])
            ->addColumn('questionsCount', TextColumnType::class, [
                'label' => 'data_table.module.questionsCount',
                'getter' => fn (Module $module) => count($module->getQuestions())
            ])
            ->addColumn('videosCount', TextColumnType::class, [
                'label' => 'data_table.module.videosCount',
                'getter' => fn (Module $module) => count($module->getVideos())
            ])
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'data_table.id'
            ])
            ->addFilter('name', StringFilterType::class, [
                'label' => 'data_table.module.name'
            ])
            ->addFilter('language', StringFilterType::class, [
                'label' => 'data_table.module.language',
                'lower' => true
            ])
        ;
    }
}
