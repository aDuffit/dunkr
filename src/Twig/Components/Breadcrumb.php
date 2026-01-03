<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Breadcrumb
{
    public array $links = []; // Ex: [['label' => 'Dunkr', 'url' => '/']]
}
