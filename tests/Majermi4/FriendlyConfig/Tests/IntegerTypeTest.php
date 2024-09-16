<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class IntegerTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable integer' => $this->simpleInteger(),
            'nullable integer' => $this->nullableInteger(),
            'nullable integer with default, value provided' => $this->nullableIntegerWithDefault(),
            'non-nullable integer with default, value provided' => $this->integerWithDefault(),
            'nullable integer with default, value omitted' => $this->nullableIntegerWithDefaultOmitted(),
            'non-nullable integer with default, value omitted' => $this->integerWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleInteger(): array
    {
        $configObject = new class(5) extends BaseTestConfig {
            public function __construct(int $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 5];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableInteger(): array
    {
        $configObject = new class(null) extends BaseTestConfig {
            public function __construct(?int $param)
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
    public function nullableIntegerWithDefault(): array
    {
        $configObject = new class(15) extends BaseTestConfig {
            public function __construct(?int $param = 10)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 15];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function integerWithDefault(): array
    {
        $configObject = new class(15) extends BaseTestConfig {
            public function __construct(int $param = 10)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 15];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableIntegerWithDefaultOmitted(): array
    {
        $configObject = new class(10) extends BaseTestConfig {
            public function __construct(?int $param = 10)
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function integerWithDefaultOmitted(): array
    {
        $configObject = new class(10) extends BaseTestConfig {
            public function __construct(int $param = 10)
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
