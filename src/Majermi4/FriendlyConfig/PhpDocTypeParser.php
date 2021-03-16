<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Kdyby\ParseUseStatements\UseStatements;
use Webmozart\Assert\Assert;

class PhpDocTypeParser
{
    public static function getParameterArrayItemType(\ReflectionParameter $parameter): string
    {
        $classReflection = $parameter->getDeclaringClass();
        $constructor = $classReflection->getConstructor();
        Assert::notNull($constructor);
        $docComment = $constructor->getDocComment();
        Assert::notNull($docComment);

        $outputArray = [];
        $success = preg_match('/@param\s+(array|iterable)<(.*,)?\s*(.+)>(\|null)?\s+\$'.$parameter->name.'/', $docComment, $outputArray);

        if (true !== (bool) $success) {
            throw new \LogicException('Constructor parameter "'.$parameter->name.'" must have a PHPDoc annotation in the following format "@param array<T> $'.$parameter->name.'" where T is a nested type.');
        }

        $arrayItemType = $outputArray[3];

        if (ParameterTypes::isSupportedType($arrayItemType)) {
            return $arrayItemType;
        }

        return self::getClassNameFromShortName($arrayItemType, $classReflection);
    }

    private static function getClassNameFromShortName(string $shortClassName, \ReflectionClass $classReflection): string
    {
        if ($classReflection->isAnonymous()) {
            $useStatementsWrapped = UseStatements::parseUseStatements(file_get_contents($classReflection->getFileName()));
            $useStatements = reset($useStatementsWrapped);
        } else {
            $useStatements = UseStatements::getUseStatements($classReflection);
        }

        if (\array_key_exists($shortClassName, $useStatements)) {
            return $useStatements[$shortClassName];
        }

        $className = $classReflection->getNamespaceName().'\\'.$shortClassName;
        if (!\class_exists($className)) {
            var_dump($classReflection);
            throw new \LogicException($className.' not found.');
        }

        return $className;
    }
}
