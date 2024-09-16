<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests\PhpDocParser;

use Majermi4\FriendlyConfig\PhpDocParser\ParameterDescriptionParser;
use PHPUnit\Framework\TestCase;

class ParameterDescriptionParserTest extends TestCase
{
    public function testParsingParameterDescription(): void
    {
        $configClass = new class(1, 'foo', [1, 2], ['foo' => 'bar']) {
            /**
             * This comment should not be processed because there exist comment in constructor doc comment.
             */
            public int $intParam;
            /**
             * Unlike previous parameter, this one is a string.
             */
            public string $stringParam;
            /**
             * @var array<int>
             */
            public array $arrayParam;
            /**
             * And this one is an array map with description in class property phpdoc and
             * it has 2 lines of description.
             *
             * @var array<string,string>
             */
            public array $stringMapParam;

            /**
             * @param int                  $intParam       This parameter is an integer
             * @param array<int>           $arrayParam     And this one is an array of integers
             * @param array<string,string> $stringMapParam
             */
            public function __construct(
                int $intParam,
                string $stringParam,
                array $arrayParam,
                array $stringMapParam,
            ) {
                $this->intParam = $intParam;
                $this->stringParam = $stringParam;
                $this->arrayParam = $arrayParam;
                $this->stringMapParam = $stringMapParam;
            }
        };

        self::assertParameterDescription($configClass, 'intParam', 'This parameter is an integer');
        self::assertParameterDescription($configClass, 'stringParam', 'Unlike previous parameter, this one is a string.');
        self::assertParameterDescription($configClass, 'arrayParam', 'And this one is an array of integers');
        self::assertParameterDescription($configClass, 'stringMapParam', 'And this one is an array map with description in class property phpdoc and it has 2 lines of description.');
    }

    private static function assertParameterDescription(
        object $configClass,
        string $parameterName,
        string $expectedDescription,
    ): void {
        $reflectionClass = new \ReflectionClass(get_class($configClass));
        /** @var \ReflectionMethod $constructor */
        $constructor = $reflectionClass->getConstructor();
        /** @var array<\ReflectionParameter> $parameters */
        $parameters = $constructor->getParameters();
        $filteredParameters = array_filter($parameters, fn (\ReflectionParameter $param) => $param->name === $parameterName);
        /** @var \ReflectionParameter $parameter */
        $parameter = reset($filteredParameters);

        $description = ParameterDescriptionParser::getParameterDescription($parameter);

        static::assertSame($expectedDescription, $description);
    }
}
