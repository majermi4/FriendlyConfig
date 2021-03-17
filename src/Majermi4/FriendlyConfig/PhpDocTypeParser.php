<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Nette\Utils\Reflection;
use Webmozart\Assert\Assert;

class PhpDocTypeParser
{
    public static function getParameterArrayItemType(\ReflectionParameter $parameter): string
    {
        $classReflection = $parameter->getDeclaringClass();
        Assert::notNull($classReflection); // TODO: Change to exception
        $constructor = $classReflection->getConstructor();
        Assert::notNull($constructor); // TODO: Change to exception
        $docComment = $constructor->getDocComment();
        Assert::string($docComment); // TODO: Change to exception

        $outputArray = [];
        $success = preg_match('/@param\s+(array|iterable)<(.*,)?\s*(.+)>(\|null)?\s+\$'.$parameter->name.'/', $docComment, $outputArray);

        if (true !== (bool) $success) {
            throw new \LogicException('Constructor parameter "'.$parameter->name.'" must have a PHPDoc annotation in the following format "@param array<T> $'.$parameter->name.'" where T is a nested type.');
        }

        $arrayItemType = $outputArray[3];

        if (ParameterTypes::isSupportedType($arrayItemType)) {
            return $arrayItemType;
        }

        return Reflection::expandClassName($arrayItemType, $classReflection);
    }
}
