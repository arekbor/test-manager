<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\TestVerify;
use App\Entity\Test;
use App\Util\AttributeHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TestVerifySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $testVerifyAttribute = AttributeHelper::getAttribute($event, TestVerify::class);
        if ($testVerifyAttribute === null) {
            return;
        }

        $arguments = $event->getArguments();
        $filtered = array_filter($arguments, fn($arg) => $arg instanceof Test);
        $test = $filtered ? reset($filtered) : null;

        if ($test === null) {
            $this->redirect($event, 'app_test_notfound');
            return;
        }

        if(!$test->isValid()) {
            $this->redirect($event, 'app_test_notvalid');
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }

    private function redirect(ControllerArgumentsEvent $event, string $routeName): void
    {   
        $url = $this->urlGenerator->generate($routeName);
        $response = new RedirectResponse($url);
        $event->setController(fn() => $response);
    }
}