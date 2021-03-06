<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Tests;

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FactoryTest extends TestCase
{
    public function testFactoryThrowsExceptionOnIncompatibleVersionId(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Liaison\CS\Config\Ruleset\RulesetInterface */
        $ruleset = $this->createMock('Liaison\CS\Config\Ruleset\RulesetInterface');
        $ruleset
            ->method('getRequiredPHPVersion')
            ->willReturn(\PHP_VERSION_ID + 2)
        ;

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage(sprintf(
            'Ruleset "%s" requires a minimum PHP version ID of "%d" but current PHP version ID is "%d".',
            $ruleset->getName(),
            $ruleset->getRequiredPHPVersion(),
            \PHP_VERSION_ID
        ));
        Factory::create($ruleset);
    }

    public function testFactoryReturnsInstanceOfConfig(): void
    {
        $config = Factory::create(new Liaison());
        $this->assertInstanceOf('PhpCsFixer\Config', $config);
    }

    public function testFactoryPassesSameRulesFromRuleset(): void
    {
        $ruleset = new Liaison();
        $config  = Factory::create($ruleset);

        $this->assertSame($ruleset->getRules(), $config->getRules());
    }

    public function testFactoryAllowsOverrideOfRules(): void
    {
        $config = Factory::create(new Liaison());
        $this->assertIsArray($config->getRules()['binary_operator_spaces']);

        $config = Factory::create(new Liaison(), [
            'binary_operator_spaces' => false,
        ]);
        $this->assertFalse($config->getRules()['binary_operator_spaces']);
    }

    public function testFactoryReturnsDefaultOptionsWhenNoOptionsGiven(): void
    {
        $config = Factory::create(new Liaison());

        $this->assertSame('.php_cs.cache', $config->getCacheFile());
        $this->assertSame([], $config->getCustomFixers());
        $this->assertInstanceOf('PhpCsFixer\Finder', $config->getFinder());
        $this->assertSame('txt', $config->getFormat());
        $this->assertFalse($config->getHideProgress());
        $this->assertSame('    ', $config->getIndent());
        $this->assertSame("\n", $config->getLineEnding());
        $this->assertNull($config->getPhpExecutable());
        $this->assertTrue($config->getRiskyAllowed());
        $this->assertTrue($config->getUsingCache());
    }

    public function testFactoryConsumesPassedOptionsToIt(): void
    {
        $options = [
            'cacheFile'     => __DIR__ . '/../../build/.php_cs.cache',
            'format'        => 'junit',
            'hideProgress'  => true,
            'indent'        => "\t",
            'lineEnding'    => "\r\n",
            'phpExecutable' => PHP_BINARY,
            'usingCache'    => false,
        ];
        $config = Factory::create(new Liaison(), [], $options);

        $this->assertSame($options['cacheFile'], $config->getCacheFile());
        $this->assertSame($options['format'], $config->getFormat());
        $this->assertTrue($config->getHideProgress());
        $this->assertSame($options['indent'], $config->getIndent());
        $this->assertSame($options['lineEnding'], $config->getLineEnding());
        $this->assertSame($options['phpExecutable'], $config->getPhpExecutable());
        $this->assertFalse($config->getUsingCache());
    }

    public function testCreateForLibraryCreatesPreformattedLicense(): void
    {
        $config = Factory::createForLibrary('Library', 'Foo Bar', 2020, 'Liaison\CS\Config\Ruleset\Liaison');
        $header = $config->getRules()['header_comment']['header'];

        $this->assertStringContainsString('This file is part of Library.', $header);
        $this->assertStringContainsString('(c) 2020 Foo Bar', $header);
    }

    public function testCreateForLibraryThrowsRuntimeExceptionForWrongRuleset(): void
    {
        $this->expectException('RuntimeException');
        Factory::createForLibrary('Library', 'Foo', 2020, 'Liaison\CS\Config\Factory');
    }
}
