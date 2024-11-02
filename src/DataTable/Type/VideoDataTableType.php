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
                'label' => 'data_table.video.uploadVideo',
                'href' => $this->urlGenerator->generate('app_video_upload', [ 
                    'id' => $options['module_id']
                ])
            ]);

        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'data_table.actions',
                'actions' => [
                    'delete' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'data_table.details',
                            'href' => function(Video $video) use($options): string {
                                return $this->urlGenerator->generate('app_video_details', [
                                    'moduleId' => $options['module_id'],
                                    'videoId' => $video->getId()
                                ]);
                            }
                        ]
                    ]
                ]
            ]);

        $builder
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'data_table.id'
            ])
            ->addColumn('video', TemplateColumnType::class, [
                'label' => 'data_table.video.video',
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
                'label' => 'data_table.id'
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
