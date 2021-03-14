<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Fixtures\FixtureConfig;
use Majermi4\FriendlyConfig\Tests\Fixtures\ScalarTypesConfig;

class FunctionalTest extends ConfigurationTestCase
{
    public function validConfigurationProvider(): array
    {
        return [
            $this->validFixtureConfiguration(),
        ];
    }

    public function validFixtureConfiguration(): array
    {
        $configObject = new FixtureConfig(
            5,
            new ScalarTypesConfig(
                1,
                'some string value',
                5.3,
                false,
            )
        );

        $validConfigValues = [
            'integerProperty' => 5,
            'scalarTypesConfig' => [
                'integerProperty' => 1,
                'stringProperty' => 'some string value',
                'floatProperty' => 5.3,
                'boolProperty' => false,
            ],
        ];

        return [$configObject, $validConfigValues];
    }
}
