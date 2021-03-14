<?php

declare(strict_types=1);

namespace majermi4\FriendlyConfig\Tests\Fixtures;

use majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class ScalarTypesConfig extends BaseTestConfig
{
    public function __construct(
        int $integerProperty,
        string $stringProperty,
        float $floatProperty,
        bool $boolProperty
    ) {
        parent::__construct(...func_get_args());
    }
}
