<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

class ParameterTypes
{
    public const BOOL = 'bool';
    public const INT = 'int';
    public const FLOAT = 'float';
    public const STRING = 'string';
    public const ARRAY = 'array';
    public const OBJECT = 'object';
    public const ITERABLE = 'iterable';

    public const SUPPORTED_PRIMITIVE_TYPES = [
        self::BOOL,
        self::INT,
        self::FLOAT,
        self::STRING,
        self::ARRAY,
        self::ITERABLE,
    ];

    /**
     * Returns true when data type is primitive data type that can be mapped to Symfony configuration tree
     * builder or a valid class that exists.
     */
    public static function isSupportedType(string $type): bool
    {
        return \in_array($type, self::SUPPORTED_PRIMITIVE_TYPES) || \class_exists($type);
    }
}
