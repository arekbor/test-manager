<?php 

declare(strict_types=1);

namespace App\DataTable\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CopyToClipboardType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $value = $view->parent->value;

        $options['clipboard_link'] = $options['clipboard_link']($value);

        $view->vars = array_merge($view->vars, [
            'clipboardLink' => $options['clipboard_link']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('clipboard_link')
            ->setAllowedTypes('clipboard_link', ['Closure'])
        ;
    }
}