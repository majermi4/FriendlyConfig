<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests\Fixtures;

use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class FixtureConfig extends BaseTestConfig
{
    /**
     * @param array<ScalarTypesConfig> $scalarTypesConfigs
     */
    public function __construct(
        int $integerProperty = 5,
        ?ScalarTypesConfig $scalarTypesConfig = null,
        array $scalarTypesConfigs = [],
        ?int $nullableIntegerProperty = null,
    ) {
        parent::__construct(...func_get_args());
    }
}
