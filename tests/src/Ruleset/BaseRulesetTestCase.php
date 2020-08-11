<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Tests\Ruleset;

use Liaison\CS\Config\Ruleset\BaseRuleset;
use Liaison\CS\Config\Ruleset\RulesetInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

/**
 * @internal
 */
abstract class BaseRulesetTestCase extends TestCase
{
    final public function testAllConfiguredRulesAreBuiltIn()
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

    final public function testAllBuiltInRulesAreConfigured()
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

    final public function testHeaderCommentFixerIsDisabledByDefault()
    {
        $rules = self::createRuleset()->getRules();

        $this->assertArrayHasKey('header_comment', $rules);
        $this->assertFalse($rules['header_comment']);
    }

    /**
     * @dataProvider headerCommentsProvider
     *
     * @param string $header
     */
    final public function testHeaderCommentFixerIsEnabledIfHeaderIsProvided(string $header)
    {
        $rules    = self::createRuleset($header)->getRules();
        $expected = [
            'header'       => trim($header),
            'comment_type' => 'PHPDoc',
        ];

        $this->assertArrayHasKey('header_comment', $rules);
        $this->assertSame($expected, $rules['header_comment']);
    }

    final public function headerCommentsProvider(): iterable
    {
        $bar     = 'baz';
        $headers = [
            'empty'          => '',
            'not-empty'      => 'foo',
            'with-line-feed' => "\n",
            'with-spaces'    => '  ',
            'with-tab'       => "\t",
            'with-heredoc'   => <<<EOD
                    A foo with {$bar}
                EOD,
            'with-nowdoc'    => <<<'EOF'
                    A foo bar
                EOF
        ];

        foreach ($headers as $header) {
            yield [$header];
        }
    }

    /**
     * @dataProvider ruleNamesProvider
     *
     * @param string $source
     * @param array  $rules
     */
    final public function testRulesAreSortedByName(string $source, array $rules)
    {
        $sorted = $rules;
        sort($sorted);

        $this->assertSame($sorted, $rules, sprintf(
            'Failed to assert that the rules in "%s" are sorted by name.',
            $source
        ));
    }

    final public function ruleNamesProvider(): iterable
    {
        $ruleset = self::createRuleset();

        return [
            [$ruleset->getName(), array_keys($ruleset->getRules())],
        ];
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
        $className  = self::getClassName();
        $reflection = new ReflectionClass($className);
        $ruleset    = $reflection->newInstance($header);

        if (!$ruleset instanceof RulesetInterface || !$ruleset instanceof BaseRuleset) {
            throw new RuntimeException(sprintf(
                'Ruleset "%s" does not implement interface "%s" or does not extend "%s".',
                $className,
                'Liaison\CS\Config\Ruleset\RulesetInterface',
                'Liaison\CS\Config\Ruleset\BaseRuleset'
            ));
        }

        return $ruleset;
    }

    /**
     * Extract the ruleset's class name.
     *
     * @return string
     */
    final protected static function getClassName(): string
    {
        $className = preg_replace('/Test$/', '', str_replace('\\Tests', '', static::class));

        if (!\is_string($className) || '' === trim($className)) {
            throw new RuntimeException(sprintf(
                'Failed resolving class name from test class name "%s".',
                static::class
            ));
        }

        return $className;
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
}
