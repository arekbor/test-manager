<?php 

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Column\Type\VideoColumnType;
use App\Entity\Video;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoDataTableType extends AbstractDataTableType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
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
            ])
            ->addColumn('id', NumberColumnType::class, [
                'label' => 'data_table.id'
            ])
            ->addColumn('video', VideoColumnType::class, [
                'label' => 'data_table.video.video',
                'getter' => fn (Video $video) => $video,
                'video_id' => function (Video $video): int {
                    return $video->getId();
                }
            ])
            ->addFilter('id', NumericFilterType::class, [
                'label' => 'data_table.id'
            ])
            ->setDefaultPaginationData(new PaginationData(page: 1, perPage: 2))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('module_id');
    }
}
