<?php

declare(strict_types=1);

namespace App\DataTable\Type;

use App\Entity\Module;
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

class ModuleDataTableType extends AbstractDataTableType
{
    public function __construct(private UrlGeneratorInterface $urlGenerator) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('create', ButtonActionType::class, [
                'label' => 'module.data.table.create.label',
                'href' => $this->urlGenerator->generate('app_module_create'),
            ]);

        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'module.data.table.actions.label',
                'actions' => [
                    'details' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'module.data.table.details.label',
                            'attr' => [
                                'class' => 'btn btn-warning'
                            ],
                            'href' => function(Module $module): string {
                                return $this->urlGenerator->generate('app_module_details', [
                                    'id' => $module->getId()
                                ]);
                            }
                        ]
                    ],
                    'addQuestion' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'module.data.table.addQuestion.label',
                            'attr' => [
                                'class' => 'btn btn-success'
                            ],
                            'href' => function(Module $module): string {
                                return $this->urlGenerator->generate('app_question_create', [
                                    'moduleId' => $module->getId()
                                ]);
                            }
                        ]
                    ]
                ]
            ]);

        $builder
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'module.data.table.id.label'
            ])
            ->addColumn('name', TextColumnType::class, [
                'label' => 'module.data.table.name.label'
            ])
            ->addColumn('language', TextColumnType::class, [
                'label' => 'module.data.table.language.label',
                'getter' => fn (Module $module) => strtoupper($module->getLanguage())
            ])
            ->addColumn('questionsCount', TextColumnType::class, [
                'label' => 'module.data.table.questionsCount.label',
                'getter' => fn (Module $module) => count($module->getQuestions())
            ]);

        $builder
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'module.data.table.id.label'
            ])
            ->addFilter('name', StringFilterType::class, [
                'label' => 'module.data.table.name.label'
            ])
            ->addFilter('language', StringFilterType::class, [
                'label' => 'module.data.table.language.label',
                'lower' => true
            ]);

        $builder
            ->setDefaultPaginationData(new PaginationData(page: 1, perPage: 10));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        
    }
}
