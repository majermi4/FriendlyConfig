<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Util;

class StringUtil
{
    public static function toSnakeCase(string $propertyName): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '_\\0', lcfirst($propertyName)));
    }

    /**
     * @return array<string>|null
     */
    public static function splitTextNewLines(string $textWithNewLines): ?array
    {
        $textLines = preg_split('/\r\n|\n|\r/', $textWithNewLines);

        if ($textLines === false) {
            return null;
        }

        return $textLines;
    }
}
