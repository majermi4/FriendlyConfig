<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\PhpDocParser;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use Majermi4\FriendlyConfig\ParameterTypes;
use Nette\Utils\Reflection;
use ReflectionClass;

class ArrayItemTypeParser
{
    public static function getParameterArrayItemType(\ReflectionParameter $parameter): ArrayItemType
    {
        $docComment = DocCommentParser::getConstructorDocComment($parameter);

        $outputArray = [];
        $success = preg_match('/@param\s+array<((string|int),)?\s*(.+)>(\|null)?\s+\$'.$parameter->name.'/', $docComment, $outputArray);

        if ((bool) $success !== true) {
            throw InvalidConfigClassException::invalidConstructorDocCommentFormat($parameter->name);
        }

        $arrayItemType = $outputArray[3];
        $arrayKeyType = $outputArray[2] === 'string' ? 'string' : 'int';

        if (!ParameterTypes::isSupportedType($arrayItemType)) {
            /** @var ReflectionClass<object> $classReflection */
            $classReflection = $parameter->getDeclaringClass();
            $arrayItemType = Reflection::expandClassName($arrayItemType, $classReflection);
        }

        return new ArrayItemType($arrayKeyType, $arrayItemType);
    }
}
