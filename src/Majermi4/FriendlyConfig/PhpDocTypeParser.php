<?php
declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Kdyby\ParseUseStatements\UseStatements;

class PhpDocTypeParser
{
    public static function getParameterArrayItemType(\ReflectionParameter $parameter) : string
    {
        $classReflection = $parameter->getDeclaringClass();
        $constructor = $classReflection->getConstructor();
        $docComment = $constructor->getDocComment();

        $outputArray = [];
        $success = preg_match('/@param\s+(array|iterable)<(.*,)?\s*(.+)>(\|null)?\s+\$'.$parameter->name.'/', $docComment, $outputArray);

        if ((bool) $success !== true) {
            throw new \LogicException('Constructor parameter "'.$parameter->name.'" must have a PHPDoc annotation in the following format "@param array<T> $'.$parameter->name.'" where T is a nested type.');
        }

        $arrayItemType = $outputArray[3];

        if (ParameterTypes::isSupportedType($arrayItemType)) {
            return $arrayItemType;
        }

        return self::getClassNameFromShortName($arrayItemType, $classReflection);
    }

    private static function getClassNameFromShortName(string $shortClassName, \ReflectionClass $classReflection) : string
    {
        $useStatements = UseStatements::getUseStatements($classReflection);
        if (\array_key_exists($shortClassName, $useStatements)) {
            return $useStatements[$shortClassName];
        }

        $className = $classReflection->getNamespaceName() . '\\' . $shortClassName;
        if (!\class_exists($className)) {
            throw new \LogicException($className . ' not found.');
        }

        return $className;
    }

}