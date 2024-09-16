<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ArrayOfFloatsTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable array of floats' => $this->simpleArrayOfFloats(),
            'nullable array of floats' => $this->nullableArrayOfFloats(),
            'nullable array of floats with default, value provided' => $this->nullableArrayOfFloatsWithDefault(),
            'non-nullable array of floats with default, value provided' => $this->arrayOfFloatsWithDefault(),
            'nullable array of floats with default, value omitted' => $this->nullableArrayOfFloatsWithDefaultOmitted(),
            'non-nullable array of floats with default, value omitted' => $this->arrayOfFloatsWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleArrayOfFloats(): array
    {
        $configObject = new class([1.5, 2.5, 3.5]) extends BaseTestConfig {
            /**
             * @param array<float> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [1.5, 2.5, 3.5]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfFloats(): array
    {
        $configObject = new class([]) extends BaseTestConfig {
            /**
             * @param array<float>|null $param
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
    public function nullableArrayOfFloatsWithDefault(): array
    {
        $configObject = new class([1.5, 2.5, 3.5]) extends BaseTestConfig {
            /**
             * @param array<float>|null $param
             */
            public function __construct(?array $param = [2.5])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [1.5, 2.5, 3.5]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfFloatsWithDefault(): array
    {
        $configObject = new class([1.5, 2.5, 3.5]) extends BaseTestConfig {
            /**
             * @param array<float> $param
             */
            public function __construct(array $param = [3.5])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [1.5, 2.5, 3.5]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfFloatsWithDefaultOmitted(): array
    {
        $configObject = new class([1.5, 2.5, 3.5]) extends BaseTestConfig {
            /**
             * @param array<float>|null $param
             */
            public function __construct(?array $param = [1.5, 2.5, 3.5])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfFloatsWithDefaultOmitted(): array
    {
        $configObject = new class([1.5, 2.5, 3.5]) extends BaseTestConfig {
            /**
             * @param array<float> $param
             */
            public function __construct(array $param = [1.5, 2.5, 3.5])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
