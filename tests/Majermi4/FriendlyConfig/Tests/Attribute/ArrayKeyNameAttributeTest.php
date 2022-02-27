<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests\Attribute;

use Majermi4\FriendlyConfig\FriendlyConfiguration;
use Majermi4\FriendlyConfig\Tests\Fixtures\ArrayKeyNameAttributeTestConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ArrayKeyNameAttributeTest extends TestCase
{
    /**
     * Test that {@see ArrayKeyName} works and that it does not crash in PHP version older than v8.0.
     */
    public function testArrayKeyNameAttribute(): void
    {
        $configuredTreeBuilder = FriendlyConfiguration::fromClass(ArrayKeyNameAttributeTestConfig::class, 'test')->getConfigTreeBuilder();

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $configuredTreeBuilder->getRootNode();
        $arrayParamDefinition = $rootNode->getChildNodeDefinitions()['param'];
        $arrayParamDefinitionRefl = new \ReflectionClass($arrayParamDefinition);
        $arrayParamKeyProperty = $arrayParamDefinitionRefl->getProperty('key');
        $arrayParamKeyProperty->setAccessible(true);
        $arrayKeyName = $arrayParamKeyProperty->getValue($arrayParamDefinition);

        if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 8) {
            static::assertSame('customKeyName', $arrayKeyName);
        } else {
            static::assertSame('name', $arrayKeyName);
        }
    }
}
