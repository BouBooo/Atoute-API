<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFilters(): array
    {
        return [
            new TwigFunction('prefix', [$this, 'getUrl'])
        ];
    }

    public function getUrl(): string
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request->getScheme() . '://' . $request->getHost();
    }
}