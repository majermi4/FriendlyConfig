<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ArrayIndexTypeTest extends ConfigurationTestCase
{
    /**
     * {@inheritDoc}
     */
    public function validConfigurationProvider(): array
    {
        return [
            'integer array keys' => $this->integerArrayKeysTest(),
            'string array keys' => $this->stringArrayKeysTest(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function integerArrayKeysTest(): array
    {
        $configObject = new class([1, 2, 3]) extends BaseTestConfig {
            /**
             * @param array<int,int> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = [
            'param' => [1, 2, 3],
        ];

        return [$configObject, $configValues];
    }

    /**
     * @return array<mixed>
     */
    public function stringArrayKeysTest(): array
    {
        $configObject = new class(['first' => 1, 'second' => 2, 'third' => 3]) extends BaseTestConfig {
            /**
             * @param array<string,int> $param
             */
            public function __construct(array $param)
            {
                parent::__construct(...func_get_args());
            }
        };
        $configValues = [
            'param' => [
                'first' => 1,
                'second' => 2,
                'third' => 3,
            ],
        ];

        return [$configObject, $configValues];
    }
}
