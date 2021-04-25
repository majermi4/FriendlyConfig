<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\PhpDocParser;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

class PhpDocParser
{
    public static function getParameterPhpDocNode(\ReflectionParameter $parameter): PhpDocTagNode
    {
        $methodPhpDoc = self::getMethodPhpDoc($parameter);
        $phpDocNode = self::parseDocComment($methodPhpDoc);

        foreach ($phpDocNode->children as $paramNode) {
            if (!$paramNode instanceof PhpDocTagNode || !$paramNode->value instanceof ParamTagValueNode) {
                continue;
            }

            $paramName = $paramNode->value->parameterName;

            if ($paramName !== '$'.$parameter->getName()) {
                continue;
            }

            return $paramNode;
        }

        throw InvalidConfigClassException::missingConstructorDocComment('xxx'); //TODO: fix wrong exception
    }

    public static function getParameterPhpDocType(\ReflectionParameter $parameter): TypeNode
    {
        /** @var ParamTagValueNode $parameterPhpDocNodeValue */
        $parameterPhpDocNodeValue = self::getParameterPhpDocNode($parameter)->value;

        return $parameterPhpDocNodeValue->type;
    }

    public static function getMethodPhpDoc(\ReflectionParameter $parameter): string
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

    public static function parseDocComment(string $docComment): PhpDocNode
    {
        $phpDocParser = new \PHPStan\PhpDocParser\Parser\PhpDocParser(new TypeParser(), new ConstExprParser());
        $tokens = (new Lexer())->tokenize($docComment);
        $tokenIterator = new TokenIterator($tokens);

        return $phpDocParser->parse($tokenIterator);
    }
}
