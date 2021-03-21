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
        /** @var ReflectionClass<object> $classReflection */
        $classReflection = $parameter->getDeclaringClass();
        $constructor = $classReflection->getConstructor();

        if ($constructor === null) {
            throw InvalidConfigClassException::missingConstructor($classReflection->getName());
        }

        $docComment = $constructor->getDocComment();

        if ($docComment === false) {
            throw InvalidConfigClassException::missingConstructorDocComment($classReflection->getName());
        }

        $outputArray = [];
        $success = preg_match('/@param\s+array<((string|int),)?\s*(.+)>(\|null)?\s+\$'.$parameter->name.'/', $docComment, $outputArray);

        if (true !== (bool) $success) {
            throw InvalidConfigClassException::invalidConstructorDocCommentFormat($parameter->name);
        }

        $arrayItemType = $outputArray[3];
        $arrayKeyType = $outputArray[2] === 'string' ? 'string' : 'int';

        if (!ParameterTypes::isSupportedType($arrayItemType)) {
            $arrayItemType = Reflection::expandClassName($arrayItemType, $classReflection);
        }

        return new ArrayItemType($arrayKeyType, $arrayItemType);
    }
}
