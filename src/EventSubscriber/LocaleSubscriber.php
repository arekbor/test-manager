<?php 

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\IgnoreLocaleSession;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale = 'en';

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($this->routeHasIgnoreLocaleSession($request)) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    private function routeHasIgnoreLocaleSession(Request $request): bool
    {
        $controller = $request->attributes->get('_controller');

        if (!$controller || !is_string($controller) || !str_contains($controller, '::')) {
            return false;
        }

        [$class, $method] = explode('::', $controller, 2);

        if (!class_exists($class)) {
            return false;
        }

        $reflectionMethod = new ReflectionMethod($class, $method);
        return !empty($reflectionMethod->getAttributes(IgnoreLocaleSession::class));
    }
}
