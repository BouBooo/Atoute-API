<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('prefix', [$this, 'getUrl'])
        ];
    }

    public function getUrl(Request $request): string
    {
        return $request->getScheme() . '://' . $request->getHost();
    }
}