<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig;
use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ArrayOfObjectsTypeTest extends ConfigurationTestCase
{
    /**
     * {@inheritDoc}
     */
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable array of objects' => $this->simpleArrayOfObjects(),
            'nullable array of objects' => $this->nullableArrayOfObjects(),
            'nullable array of objects with default, value provided' => $this->nullableArrayOfObjectsWithDefault(),
            'non-nullable array of objects with default, value provided' => $this->arrayOfObjectsWithDefault(),
            'nullable array of objects with default, value omitted' => $this->nullableArrayOfObjectsWithDefaultOmitted(),
            'non-nullable array of objects with default, value omitted' => $this->arrayOfObjectsWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleArrayOfObjects(): array
    {
        $expectedParamValue = [
            new SingleParamConfig('foo'),
            new SingleParamConfig('bar'),
        ];
        $configObject = new class($expectedParamValue) extends BaseTestConfig {
            /**
             * @param array<SingleParamConfig> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = [
            'param' => [
                ['singleParam' => 'foo'],
                ['singleParam' => 'bar'],
            ],
        ];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfObjects(): array
    {
        $configObject = new class([]) extends BaseTestConfig {
            /**
             * @param array<string>|null $param
             */
            public function __construct(?array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = [];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfObjectsWithDefault(): array
    {
        $configObject = new class(['foo']) extends BaseTestConfig {
            /**
             * @param array<string>|null $param
             */
            public function __construct(?array $param = ['bar'])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => ['foo']];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfObjectsWithDefault(): array
    {
        $configObject = new class(['foo']) extends BaseTestConfig {
            /**
             * @param array<string> $param
             */
            public function __construct(array $param = ['bar'])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => ['foo']];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfObjectsWithDefaultOmitted(): array
    {
        $configObject = new class(['foo']) extends BaseTestConfig {
            /**
             * @param array<string>|null $param
             */
            public function __construct(?array $param = ['foo'])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfObjectsWithDefaultOmitted(): array
    {
        $configObject = new class(['foo']) extends BaseTestConfig {
            /**
             * @param array<string> $param
             */
            public function __construct(array $param = ['foo'])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}