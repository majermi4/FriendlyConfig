<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\PhpDocParser;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use Majermi4\FriendlyConfig\Util\StringUtil;
use ReflectionClass;
use ReflectionParameter;

class ParameterDescriptionParser
{
    public static function getParameterDescription(ReflectionParameter $constructorParameter): ?string
    {
        $constructorDocCommentParamDescription = self::getConstructorDocCommentDescription($constructorParameter);

        if ($constructorDocCommentParamDescription !== null) {
            return $constructorDocCommentParamDescription;
        }

        return self::getPropertyDocCommentDescription($constructorParameter);
    }

    private static function getConstructorDocCommentDescription(ReflectionParameter $constructorParameter): ?string
    {
        try {
            $constructorDocComment = DocCommentParser::getConstructorDocComment($constructorParameter);
        } catch (InvalidConfigClassException $e) {
            return null;
        }

        $constructorDocCommentLines = StringUtil::splitTextNewLines($constructorDocComment);
        if ($constructorDocCommentLines === null) {
            return null;
        }

        $pregGrepResult = preg_grep('/@param\s+.+\s+\$'.$constructorParameter->name.'\s+(.*)(\s+)?(\*\/)?/', $constructorDocCommentLines);
        if (!is_array($pregGrepResult) || count($pregGrepResult) === 0) {
            return null;
        }

        $outputArray = [];
        $pregMatchSuccess = preg_match('/@param\s+.+\s+\$'.$constructorParameter->name.'\s+(.*)(\s+)?(\*\/)?/', reset($pregGrepResult), $outputArray);
        if ((bool) $pregMatchSuccess !== true) {
            return null;
        }

        return $outputArray[1];
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

        $propertyDocComment = $property->getDocComment();

        if ($propertyDocComment === false) {
            return null;
        }

        $docCommentLines = StringUtil::splitTextNewLines($propertyDocComment);
        if ($docCommentLines === null) {
            return null;
        }

        $description = '';
        foreach ($docCommentLines as $propertyDocCommentLine) {
            $outputArray = [];
            $pregMatchSuccess = preg_match('/\s+\*\s+([^@].*)(\*\/)?/', $propertyDocCommentLine, $outputArray);
            if ((bool) $pregMatchSuccess !== true) {
                continue;
            }

            $trimmedLineText = trim($outputArray[1] ?? '');
            if ($trimmedLineText === '') {
                continue;
            }

            $description .= ' '.$outputArray[1];
        }

        $trimmedDescription = trim($description);

        if ($trimmedDescription === '') {
            return null;
        }

        return $trimmedDescription;
    }
}
