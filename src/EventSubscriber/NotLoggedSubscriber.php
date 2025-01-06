<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\NotLogged;
use App\Util\AttributeHelper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NotLoggedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $isNotGrantedAttribute = AttributeHelper::getAttribute($event, NotLogged::class);
        if ($isNotGrantedAttribute === null) {
            return;
        }
        
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException("You cannot be logged in to access the resource");
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }
}
