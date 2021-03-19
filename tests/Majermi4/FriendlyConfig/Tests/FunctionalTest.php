<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\Tests\Fixtures\FixtureConfig;
use Majermi4\FriendlyConfig\Tests\Fixtures\ScalarTypesConfig;

class FunctionalTest extends ConfigurationTestCase
{
    /**
     * {@inheritDoc}
     */
    public function validConfigurationProvider(): array
    {
        return [
            'Test fixture config' => $this->validFixtureConfiguration(),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function validFixtureConfiguration(): array
    {
        $configObject = new FixtureConfig(
            5,
            new ScalarTypesConfig(
                1,
                'some string value',
                5.3,
                false,
            ),
            [],
            null,
        );

        $validConfigValues = [
            'integer_property' => 5,
            'scalar_types_config' => [
                'integer_property' => 1,
                'string_property' => 'some string value',
                'float_property' => 5.3,
                'bool_property' => false,
            ],
        ];

        return [$configObject, $validConfigValues];
    }
}
