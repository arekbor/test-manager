<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $trans,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $session = $this->requestStack->getSession();

        if (!$session instanceof FlashBagAwareSessionInterface) {
            throw new LogicException("The session does not implement " . FlashBagAwareSessionInterface::class);
        }

        $session->getFlashBag()->add('danger', $this->trans->trans('flash.exceptionResponseSubscriber.message'));

        $redirect = $this->urlGenerator->generate('app_home_index');

        $event->setResponse(new RedirectResponse($redirect));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
