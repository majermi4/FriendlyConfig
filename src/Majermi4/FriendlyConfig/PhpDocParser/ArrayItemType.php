<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\PhpDocParser;

use RuntimeException;

/**
 * Used to communicate array key/item type information.
 */
class ArrayItemType
{
    private string $keyType;
    private string $valueType;

    public function __construct(string $keyType, string $valueType)
    {
        if (!in_array($keyType, ['string', 'int'])) {
            throw new RuntimeException('$keyType must be either string or int. "'.$keyType.'" given.');
        }

        $this->keyType = $keyType;
        $this->valueType = $valueType;
    }

    public function getKeyType(): string
    {
        return $this->keyType;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }
}
