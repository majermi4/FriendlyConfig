<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Exception;

use Majermi4\FriendlyConfig\ParameterTypes;
use ReflectionParameter;
use RuntimeException;

class InvalidConfigClassException extends RuntimeException
{
    public static function missingConstructor(string $className): self
    {
        return new self(
            sprintf(
                'Class "%s" must declare a constructor so it can be used by FriendlyConfig to set Symfony config parameters.',
                $className,
            )
        );
    }

    public static function missingConstructorParameters(string $className): self
    {
        return new self(
            sprintf(
                'Class "%s" must declare at least one constructor parameter so it can be used by FriendlyConfig to set Symfony config parameters.',
                $className,
            )
        );
    }

    public static function missingConstructorParameterType(string $className, string $parameterName): self
    {
        return new self(
            sprintf(
                'Constructor parameter "%s" of class "%s" must define a type (string, int, etc) so it can be used by FriendlyConfig to set Symfony config parameters.',
                $parameterName,
                $className,
            )
        );
    }

    public static function missingConstructorDocComment(string $className): self
    {
        return new self(
            sprintf(
                'Constructor of class "%s" must define a PhpDoc with type declaration for its array types (such as "array<string>") so it can be used by FriendlyConfig to set Symfony config parameters.',
                $className,
            )
        );
    }

    public static function invalidConstructorDocCommentFormat(string $parameterName): self
    {
        return new self(
            sprintf(
                'Constructor parameter "%s" must have a PHPDoc annotation in the following format "@param array<T> $%s" where T is a nested type.',
                $parameterName,
                $parameterName,
            )
        );
    }

    public static function unsupportedConstructorParameterType(ReflectionParameter $parameter): self
    {
        /* @phpstan-ignore-next-line */
        $className = $parameter->getDeclaringClass()->getName();
        /* @phpstan-ignore-next-line */
        $parameterType = $parameter->getType()->getName();

        return new self(
            sprintf(
                'Constructor parameter "%s" of class "%s" must be one of the supported types (%s) or a valid class so it can be used by FriendlyConfig to set Symfony config parameters. "%s" given.',
                $parameter->name,
                $className,
                implode(', ', ParameterTypes::SUPPORTED_PRIMITIVE_TYPES),
                $parameterType,
            )
        );
    }

    public static function unsupportedNestedArrayType(ReflectionParameter $parameter, string $nestedType): self
    {
        /* @phpstan-ignore-next-line */
        $className = $parameter->getDeclaringClass()->getName();

        return new self(
            sprintf(
                'Constructor parameter "%s" of class "%s" must define valid nested array type so it can be used by FriendlyConfig to set Symfony config parameters. "%s" given.',
                $parameter->name,
                $className,
                $nestedType,
            )
        );
    }
}
