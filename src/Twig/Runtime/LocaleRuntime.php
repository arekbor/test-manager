<?php

namespace App\Twig\Runtime;

use App\Service\LocaleService;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class LocaleRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private LocaleService $localeService,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getLocaleLinks(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new Exception('Current request not found.');
        }

        $route = $request->attributes->get('_route');
        if (empty($route)) {
            throw new Exception('Route not found in the current request.');
        }

        $routeParams = $request->attributes->get('_route_params');
        if (empty($routeParams)) {
            throw new Exception('Route parameters not found in the current request.');
        }

        $params = array_merge($routeParams, $request->query->all());
        $currentLocale = $request->getLocale();

        foreach($this->localeService->getAllowedLocales() as $locale) {
            if ($locale !== $currentLocale) {
                $parameters = array_merge($params, ['_locale' => $locale]);
                $localeLinks[$locale] = $this->urlGenerator->generate($route, $parameters);
            }
        }

        return $localeLinks;
    }
}
