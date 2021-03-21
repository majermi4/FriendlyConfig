<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use Nette\Utils\Reflection;
use ReflectionClass;

class PhpDocTypeParser
{
    public static function getParameterArrayItemType(\ReflectionParameter $parameter): string
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
        $success = preg_match('/@param\s+(array|iterable)<(.*,)?\s*(.+)>(\|null)?\s+\$'.$parameter->name.'/', $docComment, $outputArray);

        if (true !== (bool) $success) {
            throw InvalidConfigClassException::invalidConstructorDocCommentFormat($parameter->name);
        }

        $arrayItemType = $outputArray[3];

        if (ParameterTypes::isSupportedType($arrayItemType)) {
            return $arrayItemType;
        }

        return Reflection::expandClassName($arrayItemType, $classReflection);
    }
}
