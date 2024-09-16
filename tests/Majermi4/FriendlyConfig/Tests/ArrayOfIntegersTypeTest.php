<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ArrayOfIntegersTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable array of ints' => $this->simpleArrayOfIntegers(),
            'nullable array of ints' => $this->nullableArrayOfIntegers(),
            'nullable array of ints with default, value provided' => $this->nullableArrayOfIntegersWithDefault(),
            'non-nullable array of ints with default, value provided' => $this->arrayOfIntegersWithDefault(),
            'nullable array of ints with default, value omitted' => $this->nullableArrayOfIntegersWithDefaultOmitted(),
            'non-nullable array of ints with default, value omitted' => $this->arrayOfIntegersWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleArrayOfIntegers(): array
    {
        $configObject = new class([1, 2, 3]) extends BaseTestConfig {
            /**
             * @param array<int> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [1, 2, 3]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfIntegers(): array
    {
        $configObject = new class([]) extends BaseTestConfig {
            /**
             * @param array<int>|null $param
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
    public function nullableArrayOfIntegersWithDefault(): array
    {
        $configObject = new class([1, 2, 3]) extends BaseTestConfig {
            /**
             * @param array<int>|null $param
             */
            public function __construct(?array $param = [2])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [1, 2, 3]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfIntegersWithDefault(): array
    {
        $configObject = new class([1, 2, 3]) extends BaseTestConfig {
            /**
             * @param array<int> $param
             */
            public function __construct(array $param = [3])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [1, 2, 3]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfIntegersWithDefaultOmitted(): array
    {
        $configObject = new class([1, 2, 3]) extends BaseTestConfig {
            /**
             * @param array<int>|null $param
             */
            public function __construct(?array $param = [1, 2, 3])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfIntegersWithDefaultOmitted(): array
    {
        $configObject = new class([1, 2, 3]) extends BaseTestConfig {
            /**
             * @param array<int> $param
             */
            public function __construct(array $param = [1, 2, 3])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
