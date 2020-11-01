<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Tests\Fixer;

use InvalidArgumentException;
use Liaison\CS\Config\Fixer\AbstractCustomFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerNameValidator;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

/**
 * @internal
 */
abstract class AbstractCustomFixerTestCase extends TestCase
{
    use AssertTokensTrait;

    /**
     * @var \Liaison\CS\Config\Fixer\AbstractCustomFixer
     */
    protected $fixer;

    /**
     * @var null|\PhpCsFixer\FixerNameValidator
     */
    protected static $validator;

    final protected function setUp(): void
    {
        $this->fixer = self::createFixer();

        if (null === self::$validator) {
            self::$validator = new FixerNameValidator();
        }
    }

    final public function testPriority(): void
    {
        $this->assertIsInt($this->fixer->getPriority());
    }

    final public function testFixerDefinitionStartsWithCorrectCase(): void
    {
        $this->assertMatchesRegularExpression('/^[A-Z`].*\.$/', $this->fixer->getDefinition()->getSummary());
    }

    final public function testFixerDefinitionHasExactlyOneCodeSample(): void
    {
        $this->assertCount(1, $this->fixer->getDefinition()->getCodeSamples());
    }

    final public function testCodeSampleEndsWithNewLine(): void
    {
        $codeSample = $this->fixer->getDefinition()->getCodeSamples()[0];

        $this->assertMatchesRegularExpression('/\n$/', $codeSample->getCode());
    }

    final public function testCodeSampleIsChangedDuringFixing(): void
    {
        $codeSample = $this->fixer->getDefinition()->getCodeSamples()[0];

        if ($this->fixer instanceof ConfigurableFixerInterface) {
            $this->fixer->configure($codeSample->getConfiguration());
        }

        Tokens::clearCache();
        $tokens = Tokens::fromCode($codeSample->getCode());

        $this->fixer->fix($this->createMock('SplFileInfo'), $tokens);
        $this->assertNotSame($codeSample->getCode(), $tokens->generateCode());
    }

    final public function testFixerNameIsValidForCustomFixers(): void
    {
        $this->assertTrue(self::$validator->isValid($this->fixer->getName(), true));
    }

    final protected function doTest(string $expected, ?string $input = null, array $configuration = []): void
    {
        if ($this->fixer instanceof ConfigurableFixerInterface) {
            $this->fixer->configure($configuration);
        }

        if ($expected === $input) {
            throw new InvalidArgumentException('Expected must be different from input.');
        }

        if (null !== $input) {
            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            $this->assertTrue($this->fixer->isCandidate($tokens));

            $this->fixer->fix($this->createMock('SplFileInfo'), $tokens);
            $tokens->clearEmptyTokens();

            $this->assertSame($expected, $tokens->generateCode());

            Tokens::clearCache();
            self::assertTokens(Tokens::fromCode($expected), $tokens);
        }

        $this->assertNull($this->lintSource($expected));
        $tokens = Tokens::fromCode($expected);

        $this->fixer->fix($this->createMock('SplFileInfo'), $tokens);
        $this->assertSame($expected, $tokens->generateCode());
        $this->assertFalse($tokens->isChanged());
    }

    private function lintSource(string $source): ?string
    {
        static $linter;

        if (null === $linter) {
            $linter = new Linter();
        }

        try {
            $linter->lintSource($source)->check();
        } catch (LintingException $e) {
            return sprintf('Linting "%s" failed with error: %s.', $source, $e->getMessage());
        }

        return null;
    }

    /**
     * Creates an instance of the current custom fixer.
     *
     * @return \Liaison\CS\Config\Fixer\AbstractCustomFixer
     */
    final protected static function createFixer(): AbstractCustomFixer
    {
        $className  = self::getClassName();
        $reflection = new ReflectionClass($className);
        $fixer      = $reflection->newInstance();

        if (!$fixer instanceof AbstractCustomFixer) {
            throw new RuntimeException(sprintf(
                'Custom fixer "%s" does not extend "%s".',
                $className,
                'Liaison\CS\Config\Fixer\AbstractCustomFixer'
            ));
        }

        return $fixer;
    }

    /**
     * Extract the custom fixer's class name.
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
}
