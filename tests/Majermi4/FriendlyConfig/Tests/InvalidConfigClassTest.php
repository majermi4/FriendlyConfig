<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use Majermi4\FriendlyConfig\FriendlyConfiguration;
use PHPUnit\Framework\TestCase;

class InvalidConfigClassTest extends TestCase
{
    public function testConfigClassWithMissingConstructor(): void
    {
        $configObject = new class {};

        static::expectException(InvalidConfigClassException::class);
        static::expectExceptionCode(InvalidConfigClassException::MISSING_CONSTRUCTOR);

        FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');
    }

    public function testConfigClassWithMissingConstructorParameters(): void
    {
        $configObject = new class {
            public function __construct()
            {
            }
        };

        static::expectException(InvalidConfigClassException::class);
        static::expectExceptionCode(InvalidConfigClassException::MISSING_CONSTRUCTOR_PARAMETERS);

        FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');
    }

    public function testConfigClassWithMissingConstructorDocComment(): void
    {
        $configObject = new class(['foo']) {
            public function __construct(array $param)
            {
            }
        };

        static::expectException(InvalidConfigClassException::class);
        static::expectExceptionCode(InvalidConfigClassException::MISSING_CONSTRUCTOR_DOC_COMMENT);

        FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');
    }

    public function testConfigClassWithInvalidConstructorDocCommentFormat(): void
    {
        $configObject = new class(['foo']) {
            /**
             * @param array $param <--- missing T in array<T>
             */
            public function __construct(array $param)
            {
            }
        };

        static::expectException(InvalidConfigClassException::class);
        static::expectExceptionCode(InvalidConfigClassException::INVALID_CONSTRUCTOR_DOC_COMMENT_FORMAT);

        FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');
    }

    public function testConfigClassWithUnsupportedConstructorParameterType(): void
    {
        $configObject = new class(['foo']) {
            public function __construct(iterable $param)
            {
            }
        };

        static::expectException(InvalidConfigClassException::class);
        static::expectExceptionCode(InvalidConfigClassException::UNSUPPORTED_CONSTRUCTOR_PARAMETER_TYPE);

        FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');
    }

    public function testConfigClassWithUnsupportedNestedArrayTypeArray(): void
    {
        $configObject = new class(['foo']) {
            /**
             * @param array<array> $param
             */
            public function __construct(array $param)
            {
            }
        };

        static::expectException(InvalidConfigClassException::class);
        static::expectExceptionCode(InvalidConfigClassException::UNSUPPORTED_NESTED_ARRAY_TYPE);

        FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');
    }

    public function testConfigClassWithUnsupportedNestedArrayTypeOfCallable(): void
    {
        $configObject = new class(['foo']) {
            /**
             * @param array<callable> $param
             */
            public function __construct(array $param)
            {
            }
        };

        static::expectException(InvalidConfigClassException::class);
        static::expectExceptionCode(InvalidConfigClassException::UNSUPPORTED_NESTED_ARRAY_TYPE);

        FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');
    }
}
