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

use InvalidArgumentException;
use Liaison\CS\Config\Fixer\AbstractCustomFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\FixerNameValidator;
use PhpCsFixer\Linter\CachingLinter;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use SplFileInfo;

/**
 * @internal
 * @codeCoverageIgnore
 */
abstract class AbstractCustomFixerTestCase extends TestCase
{
    use AssertTokensTrait;

    /**
     * @var null|\Liaison\CS\Config\Fixer\AbstractCustomFixer
     */
    protected $fixer;

    /**
     * @var null|\PhpCsFixer\Linter\LinterInterface
     */
    protected $linter;

    /**
     * @var null|\PhpCsFixer\FixerNameValidator
     */
    protected $validator;

    final protected function setUp(): void
    {
        parent::setUp();

        $this->fixer     = $this->createFixer();
        $this->linter    = $this->getLinter();
        $this->validator = new FixerNameValidator();
    }

    final protected function tearDown(): void
    {
        parent::tearDown();

        $this->fixer     = null;
        $this->linter    = null;
        $this->validator = null;
    }

    final public function testFixerNameIsValidForCustomFixers(): void
    {
        $this->assertTrue(
            $this->validator->isValid($this->fixer->getName(), true),
            sprintf('Fixer name "%s" is not valid.', $this->fixer->getName())
        );
    }

    final public function testFixerClassIsFinal(): void
    {
        $fixer = new ReflectionClass($this->fixer);

        $this->assertTrue(
            $fixer->isFinal(),
            sprintf('Fixer "%s" must be declared "final".', $this->fixer->getName())
        );
    }

    final public function testIsRisky(): void
    {
        $this->assertIsBool(
            $this->fixer->isRisky(),
            sprintf('Return type for ::isRisky() of "%s" is invalid.', $this->fixer->getName())
        );
        $riskyDescription = $this->fixer->getDefinition()->getRiskyDescription();

        if ($this->fixer->isRisky()) {
            $this->assertMatchesRegularExpression('/^[a-z]/', $riskyDescription);
        } else {
            $this->assertNull(
                $riskyDescription,
                sprintf('[%s] Fixer is not risky so no description of it expected.', $this->fixer->getName())
            );
        }

        $isRiskyMethod = new ReflectionMethod($this->fixer, 'isRisky');

        // If fixer is not risky then the method `isRisky` from `AbstractFixer` must be used
        $this->assertSame(
            !$this->fixer->isRisky(),
            'PhpCsFixer\AbstractFixer' === $isRiskyMethod->getDeclaringClass()->getName()
        );
    }

    final public function testFixerDefinition(): void
    {
        $this->assertInstanceOf('PhpCsFixer\Fixer\DefinedFixerInterface', $this->fixer);

        $definition          = $this->fixer->getDefinition();
        $fixerIsConfigurable = $this->fixer instanceof ConfigurableFixerInterface;

        $this->assertMatchesRegularExpression('/^[A-Z`].*$/', $definition->getSummary());

        $samples = $definition->getCodeSamples();
        $this->assertNotEmpty($samples);

        $configSamples = [];
        $fileInfo      = new StdinFileInfo();

        foreach ($samples as $counter => $sample) {
            $this->assertInstanceOf('PhpCsFixer\FixerDefinition\CodeSampleInterface', $sample);
            $this->assertIsInt($counter);

            $code = $sample->getCode();
            $this->assertIsString($code);
            $this->assertNotEmpty($code);
            $this->assertMatchesRegularExpression('/\n$/', $code);

            $config = $sample->getConfiguration();

            if (null !== $config) {
                $this->assertTrue($fixerIsConfigurable);
                $this->assertIsArray($config);

                $configSamples[$counter] = $sample;
            } elseif ($fixerIsConfigurable) {
                if (!$sample instanceof VersionSpecificCodeSampleInterface) {
                    $this->assertArrayNotHasKey('default', $configSamples);
                }

                $configSamples['default'] = true;
            }

            if ($sample instanceof VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(\PHP_VERSION_ID)) {
                continue;
            }

            if ($fixerIsConfigurable) {
                // always re-configure as the fixer might have been configured with diff. configuration form previous sample
                $this->fixer->configure(null === $config ? [] : $config);
            }

            Tokens::clearCache();
            $tokens = Tokens::fromCode($code);
            $this->fixer->fix(
                $sample instanceof FileSpecificCodeSampleInterface ? $sample->getSplFileInfo() : $fileInfo,
                $tokens
            );

            $this->assertTrue($tokens->isChanged());

            $duplicatedCodeSample = array_search(
                $sample,
                \array_slice($samples, 0, $counter),
                false
            );

            $this->assertFalse($duplicatedCodeSample);
        }
    }

    final public function testDeprecatedFixersHaveCorrectSummary(): void
    {
        $reflection = new ReflectionClass($this->fixer);
        $comment    = $reflection->getDocComment();

        $this->assertStringNotContainsString(
            'DEPRECATED',
            $this->fixer->getDefinition()->getSummary(),
            'Fixer cannot contain word "DEPRECATED" in summary'
        );

        if ($this->fixer instanceof DeprecatedFixerInterface) {
            $this->assertStringContainsString('@deprecated', $comment);
        } elseif (\is_string($comment)) {
            $this->assertStringNotContainsString('@deprecated', $comment);
        }
    }

    final public function testFixerConfigurationDefinitions(): void
    {
        if (!$this->fixer instanceof ConfigurableFixerInterface) {
            $this->addToAssertionCount(1); // not applied to the fixer without configuration

            return;
        }

        $configurationDefinition = $this->fixer->getConfigurationDefinition();

        $this->assertInstanceOf('PhpCsFixer\FixerConfiguration\FixerConfigurationResolver', $configurationDefinition);

        foreach ($configurationDefinition->getOptions() as $option) {
            $this->assertInstanceOf('PhpCsFixer\FixerConfiguration\FixerOptionInterface', $option);
            $this->assertNotEmpty($option->getDescription());

            $this->assertStringNotContainsString(
                'DEPRECATED',
                $option->getDescription(),
                'Option description cannot contain word "DEPRECATED"'
            );
        }
    }

    final public function testFixersReturnTypes(): void
    {
        $tokens      = Tokens::fromCode('<?php ');
        $emptyTokens = new Tokens();

        $this->assertIsInt(
            $this->fixer->getPriority(),
            sprintf('Return type for ::getPriority of "%s" is invalid.', $this->fixer->getName())
        );
        $this->assertIsBool(
            $this->fixer->supports(new SplFileInfo(__FILE__)),
            sprintf('Return type for ::supports of "%s" is invalid.', $this->fixer->getName())
        );

        $this->assertIsBool(
            $this->fixer->isCandidate($emptyTokens),
            sprintf('Return type for ::isCandidate with empty tokens of "%s" is invalid.', $this->fixer->getName())
        );
        $this->assertFalse($emptyTokens->isChanged());

        $this->assertIsBool(
            $this->fixer->isCandidate($tokens),
            sprintf('Return type for ::isCandidate of "%s" is invalid.', $this->fixer->getName())
        );
        $this->assertFalse($tokens->isChanged());

        $this->assertNull(
            $this->fixer->fix(new SplFileInfo(__FILE__), $emptyTokens),
            sprintf('Return type for ::fix with empty tokens of "%s" is invalid.', $this->fixer->getName())
        );
        $this->assertFalse($emptyTokens->isChanged());

        $this->assertNull(
            $this->fixer->fix(new SplFileInfo(__FILE__), $tokens),
            sprintf('Return type for ::fix of "%s" is invalid.', $this->fixer->getName())
        );
    }

    /**
     * Tests if a fixer fixes a given string to match the expected result.
     *
     * It is used both if you want to test if something is fixed or if it is not touched by the fixer.
     * It also makes sure that the expected output does not change when run through the fixer. That means that you
     * do not need two test cases like [$expected] and [$expected, $input] (where $expected is the same in both cases)
     * as the latter covers both of them.
     * This method throws an exception if $expected and $input are equal to prevent test cases that accidentally do
     * not test anything.
     *
     * @param string           $expected The expected fixer output
     * @param null|string      $input    The fixer input, or null if it should intentionally be equal to the output
     * @param null|SplFileInfo $file     The file to fix, or null if unneeded
     */
    protected function doTest($expected, $input = null, ?SplFileInfo $file = null): void
    {
        if ($expected === $input) {
            throw new InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $file            = $file ?: $this->getTestFile();
        $fileIsSupported = $this->fixer->supports($file);

        if (null !== $input) {
            $this->assertNull($this->lintSource($input));

            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            if ($fileIsSupported) {
                $this->assertTrue($this->fixer->isCandidate($tokens), 'Fixer must be a candidate for input code.');
                $this->assertFalse($tokens->isChanged(), 'Fixer must not touch Tokens on candidate check.');
                $fixResult = $this->fixer->fix($file, $tokens);
                $this->assertNull($fixResult, '->fix method must return null.');
            }

            $this->assertTrue($tokens->isChanged(), 'Tokens collection built on input code must be marked as changed after fixing.');

            $tokens->clearEmptyTokens();

            $this->assertSame(
                \count($tokens),
                \count(array_unique(array_map(static function (Token $token) {
                    return spl_object_hash($token);
                }, $tokens->toArray()))),
                'Token items inside Tokens collection must be unique.'
            );

            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($expected);
            static::assertTokens($expectedTokens, $tokens);
        }

        $this->assertNull($this->lintSource($expected));

        Tokens::clearCache();
        $tokens = Tokens::fromCode($expected);

        if ($fileIsSupported) {
            $fixResult = $this->fixer->fix($file, $tokens);
            $this->assertNull($fixResult, '->fix method must return null.');
        }

        $this->assertFalse($tokens->isChanged(), 'Tokens collection built on expected code must not be marked as changed after fixing.');
    }

    final protected function createFixer(): AbstractCustomFixer
    {
        $fixerClassName = preg_replace('/^(Liaison\\\\CS\\\\Config)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);

        return new $fixerClassName();
    }

    final protected function lintSource(string $source): ?string
    {
        try {
            $this->linter->lintSource($source)->check();
        } catch (LintingException $e) {
            return sprintf('Linting "%s" failed with error: %s.', $source, $e->getMessage());
        }

        return null;
    }

    /**
     * @param string $filename
     *
     * @return SplFileInfo
     */
    final protected function getTestFile($filename = __FILE__)
    {
        static $files = [];

        if (!isset($files[$filename])) {
            $files[$filename] = new SplFileInfo($filename);
        }

        return $files[$filename];
    }

    private function getLinter(): LinterInterface
    {
        static $linter = null;

        if (null === $linter) {
            $linter = new CachingLinter(new Linter());
        }

        return $linter;
    }
}
