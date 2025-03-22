<?php 

declare(strict_types=1);

namespace App\Infrastructure\Twig\Runtime;

use App\Domain\Exception\NotFoundException;
use App\Service\ParameterService;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class LocaleRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private ParameterService $parameterService,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getLocaleLinks(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new NotFoundException(RequestStack::class);
        }

        $route = $request->attributes->get('_route');
        if (empty($route)) {
            throw new NotFoundException(ParameterBag::class, [
                'key' => '_route'
            ]);
        }

        $routeParams = $request->attributes->get('_route_params');
        if (empty($routeParams)) {
            throw new NotFoundException(ParameterBag::class, [
                'key' => '_route_params'
            ]);
        }

        $params = array_merge($routeParams, $request->query->all());
        $currentLocale = $request->getLocale();

        foreach($this->parameterService->getAllowedLocales() as $locale) {
            if ($locale !== $currentLocale) {
                $parameters = array_merge($params, ['_locale' => $locale]);
                $localeLinks[$locale] = $this->urlGenerator->generate($route, $parameters);
            }
        }

        return $localeLinks;
    }
}
