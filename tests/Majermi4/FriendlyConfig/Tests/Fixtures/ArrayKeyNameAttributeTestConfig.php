<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests\Fixtures;

use Majermi4\FriendlyConfig\Attribute\ArrayKeyName;

    class ArrayKeyNameAttributeTestConfig
    {
        /** @var array<string,ScalarTypesConfig> */
        private array $param;

        /**
         * @param array<string,ScalarTypesConfig> $param
         */
        public function __construct(
            #[ArrayKeyName('customKeyName')]
            array $param
        ) {
            $this->param = $param;
        }

        /**
         * @return array<string,ScalarTypesConfig>
         */
        public function getParam(): array
        {
            return $this->param;
        }
    }
