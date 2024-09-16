<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ArrayOfBooleansTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable array of booleans' => $this->simpleArrayOfBooleans(),
            'nullable array of booleans' => $this->nullableArrayOfBooleans(),
            'nullable array of booleans with default, value provided' => $this->nullableArrayOfBooleansWithDefault(),
            'non-nullable array of booleans with default, value provided' => $this->arrayOfBooleansWithDefault(),
            'nullable array of booleans with default, value omitted' => $this->nullableArrayOfBooleansWithDefaultOmitted(),
            'non-nullable array of booleans with default, value omitted' => $this->arrayOfBooleansWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleArrayOfBooleans(): array
    {
        $configObject = new class([true, false]) extends BaseTestConfig {
            /**
             * @param array<bool> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [true, false]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfBooleans(): array
    {
        $configObject = new class([]) extends BaseTestConfig {
            /**
             * @param array<bool>|null $param
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
    public function nullableArrayOfBooleansWithDefault(): array
    {
        $configObject = new class([true, false]) extends BaseTestConfig {
            /**
             * @param array<bool>|null $param
             */
            public function __construct(?array $param = [false])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [true, false]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfBooleansWithDefault(): array
    {
        $configObject = new class([true, false]) extends BaseTestConfig {
            /**
             * @param array<bool> $param
             */
            public function __construct(array $param = [false])
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => [true, false]];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableArrayOfBooleansWithDefaultOmitted(): array
    {
        $configObject = new class([true, false]) extends BaseTestConfig {
            /**
             * @param array<bool>|null $param
             */
            public function __construct(?array $param = [true, false])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function arrayOfBooleansWithDefaultOmitted(): array
    {
        $configObject = new class([true, false]) extends BaseTestConfig {
            /**
             * @param array<bool> $param
             */
            public function __construct(array $param = [true, false])
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
