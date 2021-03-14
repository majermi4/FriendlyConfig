<?php

declare(strict_types=1);

namespace majermi4\FriendlyConfig\Tests;

use majermi4\FriendlyConfig\Tests\Fixtures\SingleParamConfig;
use majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ObjectTypeTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable object' => $this->simpleObject(),
            'nullable object' => $this->nullableObject(),
            'nullable object with default, value provided' => $this->nullableObjectWithDefault(),
            'non-nullable object with default, value provided' => $this->objectWithDefault(),
            'nullable object with default, value omitted' => $this->nullableObjectWithDefaultOmitted(),
            'non-nullable object with default, value omitted' => $this->objectWithDefaultOmitted(),
        ];
    }

    public function simpleObject(): array
    {
        $configObject = new class(new SingleParamConfig('foo')) extends BaseTestConfig {
            public function __construct(SingleParamConfig $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = [
            'param' => [
                'singleParam' => 'foo'
            ]
        ];

        return [$configObject, $configValues];
    }

    public function nullableObject(): array
    {
        $configObject = new class(null) extends BaseTestConfig {
            public function __construct(?SingleParamConfig $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = [];

        return [$configObject, $configValues];
    }

    public function nullableObjectWithDefault(): array
    {
        $configObject = new class(new SingleParamConfig('foo')) extends BaseTestConfig {
            public function __construct(?SingleParamConfig $param = null)
            {
                parent::__construct(...[$param ?? new SingleParamConfig('bar')]);
            }
        };
        $configValues = [
            'param' => [
                'singleParam' => 'foo'
            ]
        ];

        return [$configObject, $configValues];
    }

    public function objectWithDefault(): array
    {
        $configObject = new class(new SingleParamConfig('foo')) extends BaseTestConfig {
            public function __construct(SingleParamConfig $param = null)
            {
                parent::__construct(...[$param ?? new SingleParamConfig('bar')]);
            }
        };
        $configValues = [
            'param' => [
                'singleParam' => 'foo'
            ]
        ];

        return [$configObject, $configValues];
    }

    public function nullableObjectWithDefaultOmitted(): array
    {
        $configObject = new class(new SingleParamConfig('foo')) extends BaseTestConfig {
            public function __construct(?SingleParamConfig $param = null)
            {
                parent::__construct(...[$param ?? new SingleParamConfig('foo')]);
            }
        };

        return [$configObject, []];
    }

    public function objectWithDefaultOmitted(): array
    {
        $configObject = new class(new SingleParamConfig('foo')) extends BaseTestConfig {
            public function __construct(SingleParamConfig $param = null)
            {
                parent::__construct(...[$param ?? new SingleParamConfig('foo')]);
            }
        };

        return [$configObject, []];
    }
}
