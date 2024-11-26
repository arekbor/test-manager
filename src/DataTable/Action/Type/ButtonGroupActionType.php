<?php 

declare(strict_types=1);

namespace App\DataTable\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ButtonGroupActionType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if ($view->parent instanceof ColumnValueView) {
            $value = $view->parent->value;

            foreach ($options['buttons'] as $index => $item) {
                if (empty($item['href'])) {
                    break;
                }

                if (!is_callable($item['href'])) {
                    break;
                }

                $options['buttons'][$index]['href'] = $item['href']($value);
            }
        }

        $view->vars = array_replace($view->vars, [
            'buttons' => $options['buttons'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'buttons' => []
            ])
            ->setAllowedTypes('buttons', 'array')
        ;
    }
}