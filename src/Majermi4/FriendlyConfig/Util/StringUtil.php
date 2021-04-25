<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Util;

class StringUtil
{
    public static function toSnakeCase(string $propertyName): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '_\\0', lcfirst($propertyName)));
    }
}
