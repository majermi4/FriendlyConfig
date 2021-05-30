<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\PhpDocParser;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use Majermi4\FriendlyConfig\ParameterTypes;
use Nette\Utils\Reflection;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use ReflectionClass;

class ArrayItemTypeParser
{
    public static function getParameterArrayItemType(\ReflectionParameter $parameter): ArrayItemType
    {
        $parameterType = PhpDocParser::getParameterPhpDocType($parameter);
        /** @var array<IdentifierTypeNode> $genericTypes */
        $genericTypes = self::getArrayTypeNode($parameterType, $parameter->name)->genericTypes;

        if (\count($genericTypes) === 1) {
            $arrayKeyType = ParameterTypes::INT;
            $arrayItemType = $genericTypes[0]->name;
        } elseif (\count($genericTypes) === 2) {
            $arrayKeyType = $genericTypes[0]->name === 'string' ? 'string' : 'int';
            $arrayItemType = $genericTypes[1]->name ?? ParameterTypes::MIXED;
        } else {
            throw InvalidConfigClassException::invalidConstructorDocCommentFormat($parameter->name);
        }

        if (!ParameterTypes::isSupportedType($arrayItemType)) {
            /** @var ReflectionClass<object> $classReflection */
            $classReflection = $parameter->getDeclaringClass();
            $arrayItemType = Reflection::expandClassName($arrayItemType, $classReflection);
        }

        return new ArrayItemType($arrayKeyType, $arrayItemType);
    }

    private static function getArrayTypeNode(TypeNode $parameterType, string $parameterName): GenericTypeNode
    {
        if ($parameterType instanceof GenericTypeNode) {
            return $parameterType;
        }

        if ($parameterType instanceof UnionTypeNode) {
            foreach ($parameterType->types as $type) {
                if ($type instanceof GenericTypeNode) {
                    return $type;
                }
            }
        }

        throw InvalidConfigClassException::invalidConstructorDocCommentFormat($parameterName);
    }
}
