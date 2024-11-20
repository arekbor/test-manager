<?php 

declare(strict_types=1);

namespace App\DataTable\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\AbstractColumnType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VideoColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if ($view instanceof ColumnValueView) {
            if (is_callable($options['video_id'])) {
                $options['video_id'] = $options['video_id']($view->value);
            }
        }

        $view->vars = array_merge($view->vars, [
            'videoId' => $options['video_id']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'video_id'
            ])
            ->setAllowedTypes('video_id', ['int', 'Closure'])
        ;
    }
}