<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class StringTypeTest extends ConfigurationTestCase
{
    /**
     * {@inheritDoc}
     */
    public function validConfigurationProvider(): array
    {
        return [
            'non-nullable string' => $this->simpleString(),
            'nullable string' => $this->nullableString(),
            'nullable string with default, value provided' => $this->nullableStringWithDefault(),
            'non-nullable string with default, value provided' => $this->stringWithDefault(),
            'nullable string with default, value omitted' => $this->nullableStringWithDefaultOmitted(),
            'non-nullable string with default, value omitted' => $this->stringWithDefaultOmitted(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function simpleString(): array
    {
        $configObject = new class('foo') extends BaseTestConfig {
            public function __construct(string $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 'foo'];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableString(): array
    {
        $configObject = new class(null) extends BaseTestConfig {
            public function __construct(?string $param)
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
    public function nullableStringWithDefault(): array
    {
        $configObject = new class('foo') extends BaseTestConfig {
            public function __construct(?string $param = 'bar')
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 'foo'];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function stringWithDefault(): array
    {
        $configObject = new class('foo') extends BaseTestConfig {
            public function __construct(string $param = 'bar')
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = ['param' => 'foo'];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function nullableStringWithDefaultOmitted(): array
    {
        $configObject = new class('foo') extends BaseTestConfig {
            public function __construct(?string $param = 'foo')
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }

    /**
     * @return array<mixed>
     */
    public function stringWithDefaultOmitted(): array
    {
        $configObject = new class('foo') extends BaseTestConfig {
            public function __construct(string $param = 'foo')
            {
                parent::__construct(...func_get_args());
            }
        };

        return [$configObject, []];
    }
}
