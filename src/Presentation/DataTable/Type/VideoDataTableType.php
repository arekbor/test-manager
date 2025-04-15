<?php 

declare(strict_types = 1);

namespace App\Presentation\DataTable\Type;

use App\Application\Video\Model\VideoViewModel;
use App\Presentation\DataTable\Action\Type\UploadFileActionType;
use App\Presentation\DataTable\Column\Type\TruncatedTextColumnType;
use App\Presentation\Util\ByteConversion;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class VideoDataTableType extends AbstractDataTableType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('uploadVideo', UploadFileActionType::class, [
                'label' => 'data_table.video.uploadVideo',
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'upload_url' => function() use($options): string {
                    return $this->urlGenerator->generate('app_video_upload', [
                        'id' => $options['module_id']
                    ]);
                }
            ])
        ;

        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'label' => 'data_table.actions',
                'actions' => [
                    'details' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'label' => 'data_table.details',
                            'href' => function(VideoViewModel $videoViewModel): string {
                                return $this->urlGenerator->generate('app_video_details', [
                                    'moduleId' => $videoViewModel->getModuleId(),
                                    'videoId' => $videoViewModel->getId()
                                ]);
                            }
                        ]
                    ]
                ]
            ])
            ->addColumn('originalName', TruncatedTextColumnType::class, [
                'label' => 'data_table.video.originalName',
            ])
            ->addColumn('mimeType', TruncatedTextColumnType::class, [
                'label' => 'data_table.video.mimeType',
            ])
            ->addColumn('size', TruncatedTextColumnType::class, [
                'label' => 'data_table.video.size',
                'getter' => fn(VideoViewModel $videoViewModel) => ByteConversion::formatBytes($videoViewModel->getSize())
            ])
            ->addFilter('originalName', StringFilterType::class, [
                'label' => 'data_table.video.originalName',
                'lower' => true,
            ])
            ->addFilter('mimeType', StringFilterType::class, [
                'label' => 'data_table.video.mimeType',
                'lower' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('module_id')
        ;
    }
}
