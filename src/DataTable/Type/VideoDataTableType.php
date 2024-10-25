<?php declare(strict_types=1);

namespace App\DataTable\Type;

use App\Entity\Video;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoDataTableType extends BaseDataTableType
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('uploadVideo', ButtonActionType::class, [
                'label' => 'video.data.table.uploadVideo.label',
                'href' => $this->urlGenerator->generate('app_video_upload', [ 
                    'id' => $options['module_id']
                ])
            ]);

        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'video.data.table.actions.label',
                'actions' => [
                    'delete' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'video.data.table.delete.label',
                            'attr' => [
                                'class' => 'btn btn-danger'
                            ]
                        ]
                    ]
                ]
            ]);

        $builder
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'video.data.table.id.label'
            ])
            ->addColumn('video', TemplateColumnType::class, [
                'label' => 'video.data.table.video.label',
                'getter' => fn (Video $video) => $video,
                'template_path' => 'video/data_table_template.html.twig',
                'template_vars' => function(Video $video) {
                    return [
                        'video_id' => $video->getId()
                    ];
                }
            ]);

        $builder
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'video.data.table.id.label'
            ]);
        
        $builder
            ->setDefaultPaginationData(new PaginationData(page: 1, perPage: 1));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('module_id');
    }
}
