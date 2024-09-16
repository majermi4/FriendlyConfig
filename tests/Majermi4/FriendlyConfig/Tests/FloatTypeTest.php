<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class FloatTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable float' => $this->simpleFloat(),
            'nullable float' => $this->nullableFloat(),
            'nullable float with default, value provided' => $this->nullableFloatWithDefault(),
            'non-nullable float with default, value provided' => $this->floatWithDefault(),
            'nullable float with default, value omitted' => $this->nullableFloatWithDefaultOmitted(),
            'non-nullable float with default, value omitted' => $this->floatWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleFloat(): array
    {
        $configObject = new class(5.5) extends BaseTestConfig {
            public function __construct(float $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 5.5];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableFloat(): array
    {
        $configObject = new class(null) extends BaseTestConfig {
            public function __construct(?float $param)
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
    public function nullableFloatWithDefault(): array
    {
        $configObject = new class(15.5) extends BaseTestConfig {
            public function __construct(?float $param = 10.5)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 15.5];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function floatWithDefault(): array
    {
        $configObject = new class(15.5) extends BaseTestConfig {
            public function __construct(float $param = 10.5)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 15.5];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableFloatWithDefaultOmitted(): array
    {
        $configObject = new class(10.5) extends BaseTestConfig {
            public function __construct(?float $param = 10.5)
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function floatWithDefaultOmitted(): array
    {
        $configObject = new class(10.5) extends BaseTestConfig {
            public function __construct(float $param = 10.5)
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
