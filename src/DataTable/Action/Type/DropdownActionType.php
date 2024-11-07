<?php declare(strict_types=1);

namespace App\DataTable\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DropdownActionType extends AbstractActionType
{   
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if ($view->parent instanceof ColumnValueView) {
            $value = $view->parent->value;

            foreach ($options['dropdown_items'] as $index => $item) {
                if (is_callable($item['href'])) {
                    $options['dropdown_items'][$index]['href'] = $item['href']($value);
                }
            }
        }

        $view->vars = array_replace($view->vars, [
            'dropdownLabel' => $options['dropdown_label'],
            'dropdownItems' => $options['dropdown_items'],
            'dropdownClass' => $options['dropdown_class'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'dropdown_label' => 'Dropdown label',
                'dropdown_items' => [],
                'dropdown_class' => ''
            ])
            ->setAllowedTypes('dropdown_label', 'string')
            ->setAllowedTypes('dropdown_items', 'array')
            ->setAllowedTypes('dropdown_class', 'string')
        ;
    }
}