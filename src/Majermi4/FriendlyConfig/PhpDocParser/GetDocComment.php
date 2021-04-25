<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\PhpDocParser;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;

class GetDocComment
{
    public static function fromParameter(\ReflectionParameter $parameter): string
    {
        $constructor = $parameter->getDeclaringFunction();
        $docComment = $constructor->getDocComment();

        if ($docComment === false) {
            /** @var \ReflectionClass<object> $declaringClass */
            $declaringClass = $parameter->getDeclaringClass();
            $declaringClassName = $declaringClass->getName();

            throw InvalidConfigClassException::missingConstructorDocComment($declaringClassName);
        }

        return $docComment;
    }
}
