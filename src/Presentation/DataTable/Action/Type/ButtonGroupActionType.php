<?php 

declare(strict_types=1);

namespace App\Presentation\DataTable\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ButtonGroupActionType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $buttons = $options['buttons'];

        if ($view->parent instanceof ColumnValueView) {
            $value = $view->parent->value;
            foreach ($buttons as $index => $item) {
                if (!empty($item['href']) && is_callable($item['href'])) {
                    $buttons[$index]['href'] = $item['href']($value);
                }

                if (!empty($item['visible']) && is_callable($item['visible'])) {
                    $buttons[$index]['visible'] = $item['visible']($value);
                }
            }
        }

        $view->vars = array_replace($view->vars, [
            'buttons' => $buttons,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['buttons' => []])
            ->setAllowedTypes('buttons', 'array')
            ->setNormalizer('buttons', function ($res, array $buttons): array {
                $buttonResolver = (new OptionsResolver())
                    ->setDefaults([
                        'label' => '',
                        'visible' => true,
                        'href' => null,
                        'attr' => []
                    ])
                    ->setAllowedTypes('label', 'string')
                    ->setAllowedTypes('visible', ['bool', 'callable'])
                    ->setAllowedTypes('href', ['null', 'string', 'callable'])
                    ->setAllowedTypes('attr', 'array');
            
                return array_map(fn($button) => $buttonResolver->resolve($button), $buttons);
            })
        ;
    }
}