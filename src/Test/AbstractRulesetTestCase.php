<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Test;

use Liaison\CS\Config\Ruleset\RulesetInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @codeCoverageIgnore
 */
abstract class AbstractRulesetTestCase extends TestCase
{
    final public static function headerCommentsProvider(): iterable
    {
        $headers = [
            'empty'          => '',
            'not-empty'      => 'foo',
            'with-line-feed' => "\n",
            'with-spaces'    => '  ',
            'with-tab'       => "\t",
        ];

        foreach ($headers as $header) {
            yield [$header];
        }
    }

    final public static function ruleNamesProvider(): iterable
    {
        $ruleset = self::createRuleset();

        return [
            [$ruleset->getName(), array_keys($ruleset->getRules())],
        ];
    }

    final public function testAllConfiguredRulesAreBuiltIn(): void
    {
        $fixersNotBuiltIn = array_diff(
            $this->configuredFixers(),
            $this->builtInFixers()
        );

        sort($fixersNotBuiltIn);
        $c = \count($fixersNotBuiltIn);

        $this->assertEmpty($fixersNotBuiltIn, sprintf(
            'Failed to assert that %s for the %s "%s" %s built-in to PhpCsFixer.',
            $c > 1 ? 'fixers' : 'fixer',
            $c > 1 ? 'rules' : 'rule',
            implode('", "', $fixersNotBuiltIn),
            $c > 1 ? 'are' : 'is'
        ));
    }

    final public function testAllBuiltInRulesAreConfigured(): void
    {
        $fixersWithoutConfiguration = array_diff(
            $this->builtInFixers(),
            $this->configuredFixers()
        );

        sort($fixersWithoutConfiguration);
        $c = \count($fixersWithoutConfiguration);

        $this->assertEmpty($fixersWithoutConfiguration, sprintf(
            'Failed to assert that built-in %s for the %s "%s" %s configured in this ruleset.',
            $c > 1 ? 'fixers' : 'fixer',
            $c > 1 ? 'rules' : 'rule',
            implode('", "', $fixersWithoutConfiguration),
            $c > 1 ? 'are' : 'is'
        ));
    }

    final public function testHeaderCommentFixerIsDisabledByDefault(): void
    {
        $rules = self::createRuleset()->getRules();

        $this->assertArrayHasKey('header_comment', $rules);
        $this->assertFalse($rules['header_comment']);
    }

    /**
     * @dataProvider headerCommentsProvider
     *
     * @param string $header
     *
     * @return void
     */
    final public function testHeaderCommentFixerIsEnabledIfHeaderIsProvided(string $header): void
    {
        $rules    = self::createRuleset($header)->getRules();
        $expected = [
            'header'       => trim($header),
            'comment_type' => 'PHPDoc',
        ];

        $this->assertArrayHasKey('header_comment', $rules);
        $this->assertSame($expected, $rules['header_comment']);
    }

    /**
     * @dataProvider ruleNamesProvider
     *
     * @param string $source
     * @param array  $rules
     *
     * @return void
     */
    final public function testRulesAreSortedByName(string $source, array $rules): void
    {
        $sorted = $rules;
        sort($sorted);

        $this->assertSame($sorted, $rules, sprintf(
            'Failed to assert that the rules in "%s" are sorted by name.',
            $source
        ));
    }

    /**
     * Rules defined by PhpCsFixer.
     *
     * @return string[]
     */
    private function builtInFixers(): array
    {
        static $builtInFixers;

        if (null === $builtInFixers) {
            $fixerFactory = FixerFactory::create();
            $fixerFactory->registerBuiltInFixers();

            $builtInFixers = array_map(static function (FixerInterface $fixer): string {
                return $fixer->getName();
            }, $fixerFactory->getFixers());
        }

        return $builtInFixers;
    }

    /**
     * Rules defined by this ruleset.
     *
     * @return string[]
     */
    private function configuredFixers(): array
    {
        $rules = array_map(static function ($ruleConfiguration) {
            return true;
        }, self::createRuleset()->getRules());

        return array_keys(RuleSet::create($rules)->getRules());
    }

    /**
     * Creates an instance of the current ruleset.
     *
     * @param null|string $header
     *
     * @return \Liaison\CS\Config\Ruleset\RulesetInterface
     */
    final protected static function createRuleset(?string $header = null): RulesetInterface
    {
        $className = preg_replace('/^(Liaison\\\\CS\\\\Config)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);

        return new $className($header);
    }
}
