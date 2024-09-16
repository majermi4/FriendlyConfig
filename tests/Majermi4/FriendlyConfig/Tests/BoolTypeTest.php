<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class BoolTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable bool' => $this->simpleBool(),
            'nullable bool' => $this->nullableBool(),
            'nullable bool with default, value provided' => $this->nullableBoolWithDefault(),
            'non-nullable bool with default, value provided' => $this->boolWithDefault(),
            'nullable bool with default, value omitted' => $this->nullableBoolWithDefaultOmitted(),
            'non-nullable bool with default, value omitted' => $this->boolWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleBool(): array
    {
        $configObject = new class(true) extends BaseTestConfig {
            public function __construct(bool $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => true];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableBool(): array
    {
        $configObject = new class(null) extends BaseTestConfig {
            public function __construct(?bool $param)
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
    public function nullableBoolWithDefault(): array
    {
        $configObject = new class(true) extends BaseTestConfig {
            public function __construct(?bool $param = false)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => true];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function boolWithDefault(): array
    {
        $configObject = new class(true) extends BaseTestConfig {
            public function __construct(bool $param = false)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => true];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableBoolWithDefaultOmitted(): array
    {
        $configObject = new class(false) extends BaseTestConfig {
            public function __construct(?bool $param = false)
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function boolWithDefaultOmitted(): array
    {
        $configObject = new class(false) extends BaseTestConfig {
            public function __construct(bool $param = false)
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
