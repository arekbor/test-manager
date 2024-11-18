<?php declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Action\Type\DropdownActionType;
use App\Entity\Module;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;

class ModuleDataTableType extends BaseDataTableType
{
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
                        'type' => DropdownActionType::class,
                        'type_options' => [
                            'dropdown_label' => 'data_table.actions',
                            'dropdown_items' => [
                                [
                                    'label' => 'data_table.details',
                                    'href' => function(Module $module): string {
                                        return $this->urlGenerator->generate('app_module_questions', [
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
                                    'label' => 'data_table.module.uploadVideo',
                                    'href' => function(Module $module): string {
                                        return $this->urlGenerator->generate('app_video_upload', [
                                            'id' => $module->getId()
                                        ]);
                                    }
                                ],
                                [
                                    'label' => 'data_table.module.createTest',
                                    'href' => function(): string {
                                        return $this->urlGenerator->generate('app_home_index');
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
            ->addColumn('name', TextColumnType::class, [
                'label' => 'data_table.module.name',
                'getter' => fn(Module $module) => $this->trimText($module->getName())
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
