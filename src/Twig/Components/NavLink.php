<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class NavLink
{
    public string $url = '#';
    public string $icon; // ex: "lucide:users"
    public string $label;
    public bool $active = false;
}
