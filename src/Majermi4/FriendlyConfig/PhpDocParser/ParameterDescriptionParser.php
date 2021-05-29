<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\PhpDocParser;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use ReflectionClass;
use ReflectionParameter;

class ParameterDescriptionParser
{
    public static function getParameterDescription(ReflectionParameter $constructorParameter): ?string
    {
        $constructorDocCommentParamDescription = self::getConstructorDocCommentDescription($constructorParameter);

        if ($constructorDocCommentParamDescription !== null && $constructorDocCommentParamDescription !== '') {
            return $constructorDocCommentParamDescription;
        }

        return self::getPropertyDocCommentDescription($constructorParameter);
    }

    private static function getConstructorDocCommentDescription(ReflectionParameter $constructorParameter): ?string
    {
        try {
            $parameterTypeNode = PhpDocParser::getParameterPhpDocNode($constructorParameter);
        } catch (InvalidConfigClassException $e) {
            return null;
        }

        /** @var ParamTagValueNode $parameterTypeNodeValue */
        $parameterTypeNodeValue = $parameterTypeNode->value;

        return $parameterTypeNodeValue->description;
    }

    private static function getPropertyDocCommentDescription(ReflectionParameter $constructorParameter): ?string
    {
        /** @var ReflectionClass<object> $declaringClass */
        $declaringClass = $constructorParameter->getDeclaringClass();

        try {
            $property = $declaringClass->getProperty($constructorParameter->name);
        } catch (\ReflectionException $e) {
            return null;
        }

        $propertyDocComment = (string) $property->getDocComment();

        if ($propertyDocComment === '') {
            return null;
        }

        $phpDocNode = PhpDocParser::parseDocComment($propertyDocComment);

        $description = '';
        foreach ($phpDocNode->children as $phpDocChildNode) {
            if ($phpDocChildNode instanceof PhpDocTextNode) {
                // Replaces new lines with spaces
                $description .= trim((string) preg_replace('/\s+/', ' ', $phpDocChildNode->text));
            }
        }

        $trimmedDescription = trim($description);

        if ($trimmedDescription === '') {
            return null;
        }

        return $trimmedDescription;
    }
}
