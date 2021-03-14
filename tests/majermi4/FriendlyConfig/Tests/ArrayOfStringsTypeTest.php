<?php

declare(strict_types=1);

namespace majermi4\FriendlyConfig\Tests;

use majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ArrayOfStringsTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable array of strings' => $this->simpleArrayOfStrings(),
            'nullable array of strings' => $this->nullableArrayOfStrings(),
            'nullable array of strings with default, value provided' => $this->nullableArrayOfStringsWithDefault(),
            'non-nullable array of string with default, value provided' => $this->arrayOfStringsWithDefault(),
            'nullable array of string with default, value omitted' => $this->nullableArrayOfStringsWithDefaultOmitted(),
            'non-nullable string with default, value omitted' => $this->arrayOfStringsWithDefaultOmitted(),
        ];
    }

    public function simpleArrayOfStrings(): array
    {
        $configObject = new class(['foo']) extends BaseTestConfig {
            /**
             * @param array<string> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => ['foo']];

        return [$configObject, $configValues];
    }

    public function nullableArrayOfStrings(): array
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

    public function nullableArrayOfStringsWithDefault(): array
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

    public function arrayOfStringsWithDefault(): array
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

    public function nullableArrayOfStringsWithDefaultOmitted(): array
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

    public function arrayOfStringsWithDefaultOmitted(): array
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
