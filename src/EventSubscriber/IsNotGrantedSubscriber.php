<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\IsNotGranted;
use App\Util\AttributeHelper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class IsNotGrantedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $isNotGrantedAttribute = AttributeHelper::getAttribute($event, IsNotGranted::class);
        if ($isNotGrantedAttribute === null) {
            return;
        }
        
        $isNotGranted = $isNotGrantedAttribute[0];

        if ($this->security->isGranted($isNotGranted->role)) {
            throw new AccessDeniedException("The role '" . $isNotGranted->role . "' is not allowed to access this resource.");
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }
}
