<?php

declare(strict_types=1);

namespace App\Presentation\DataTable\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UploadFileActionType extends AbstractActionType
{
    /**
     * @param array<string, mixed> $options
     */
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $options['upload_url'] = $options['upload_url']($view);

        $view->vars = array_merge($view->vars, [
            'uploadUrl' => $options['upload_url']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('upload_url')
            ->setAllowedTypes('upload_url', ['Closure'])
        ;
    }
}
