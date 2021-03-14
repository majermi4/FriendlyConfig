<?php
declare(strict_types=1);

namespace majermi4\FriendlyConfig\Tests\Fixtures;

use majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;

class SingleParamConfig extends BaseTestConfig
{
    public function __construct(string $singleParam)
    {
        parent::__construct(...func_get_args());
    }
}