<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class ArrayKeyName
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
