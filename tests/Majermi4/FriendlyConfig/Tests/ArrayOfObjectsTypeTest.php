<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig;
use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ArrayOfObjectsTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable array of objects' => $this->simpleArrayOfObjects(),
            'nullable array of objects' => $this->nullableArrayOfObjects(),
            'nullable array of objects with default, value provided' => $this->nullableArrayOfObjectsWithDefault(),
            'non-nullable array of objects with default, value provided' => $this->arrayOfObjectsWithDefault(),
            'nullable array of objects with default, value omitted' => $this->nullableArrayOfObjectsWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleArrayOfObjects(): array
    {
        $expectedParamValue = [
            'idx1' => new SingleParamConfig('foo'),
            'idx2' => new SingleParamConfig('bar'),
        ];
        $configObject = new class($expectedParamValue) extends BaseTestConfig {
            /**
             * @param array<string,\Majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = [
            'param' => [
                'idx1' => ['single_param' => 'foo'],
                'idx2' => ['single_param' => 'bar'],
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
             * @param array<string,\Majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig>|null $param
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
        $configObject = new class(['idx' => new SingleParamConfig('foo')]) extends BaseTestConfig {
            /**
             * @param array<string,\Majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig>|null $param
             */
            public function __construct(?array $param)
            {
                parent::__construct(...[$param ?? [new SingleParamConfig('bar')]]);
            }
        };
        $configValues = [
            'param' => [
                'idx' => ['single_param' => 'foo'],
            ],
        ];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfObjectsWithDefault(): array
    {
        $configObject = new class(['idx' => new SingleParamConfig('foo')]) extends BaseTestConfig {
            /**
             * @param array<string,\Majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...[$param]);
            }
        };
        $configValues = [
            'param' => [
                'idx' => ['single_param' => 'foo'],
            ],
        ];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfObjectsWithDefaultOmitted(): array
    {
        $configObject = new class([]) extends BaseTestConfig {
            /**
             * @param array<\Majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig>|null $param
             */
            public function __construct(?array $param)
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
