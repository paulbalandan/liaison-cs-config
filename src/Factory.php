<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config;

use Liaison\CS\Config\Ruleset\RulesetInterface;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use RuntimeException;

final class Factory
{
    /**
     * An enhancement to self::create() that creates a pre-formatted license header.
     *
     * @param string $library
     * @param string $author
     * @param int    $initialLicenseYear
     * @param string $rulesetName
     * @param array  $overrides
     * @param array  $options
     *
     * @throws RuntimeException
     *
     * @return \PhpCsFixer\Config
     */
    public static function createForLibrary(
        string $library,
        string $author,
        int $initialLicenseYear,
        string $rulesetName,
        array $overrides = [],
        array $options = []
    ) {
        $header = <<<HEADER
            This file is part of {$library}.

            (c) {$initialLicenseYear} {$author}

            For the full copyright and license information, please view the LICENSE
            file that was distributed with this source code.
            HEADER;

        $ruleset = new $rulesetName($header);

        if (!$ruleset instanceof RulesetInterface) {
            throw new RuntimeException(sprintf(
                'Ruleset "%s" does not implement interface "%s".',
                $rulesetName,
                'Liaison\CS\Config\Ruleset\RulesetInterface'
            ));
        }

        return self::create($ruleset, $overrides, $options);
    }

    /**
     * Creates a new `PhpCsFixer\Config` instance with the
     * passed custom ruleset.
     *
     * @param \Liaison\CS\Config\Ruleset\RulesetInterface $ruleset   Ruleset class
     * @param array                                       $overrides Rules to override in the ruleset
     * @param array                                       $options   Other options to pass to `PhpCsFixer\Config`
     *
     * @throws RuntimeException
     *
     * @return \PhpCsFixer\Config
     */
    public static function create(RulesetInterface $ruleset, array $overrides = [], array $options = []): Config
    {
        if (\PHP_VERSION_ID < $ruleset->getRequiredPHPVersion()) {
            throw new RuntimeException(sprintf(
                'Ruleset "%s" requires a minimum PHP version ID of "%d" but current PHP version ID is "%d".',
                $ruleset->getName(),
                $ruleset->getRequiredPHPVersion(),
                \PHP_VERSION_ID
            ));
        }

        // Meant to be used in vendor/ to get to the root directory
        $dir = \dirname(__DIR__, 4);
        $dir = realpath($dir) ?: $dir;

        $defaultFinder = Finder::create()
            ->files()
            ->in([$dir])
            ->exclude(['build'])
        ;

        // Resolve Config options
        $cacheFile      = $options['cacheFile'] ?? '.php_cs.cache';
        $customFixers   = $options['customFixers'] ?? [];
        $finder         = $options['finder'] ?? $defaultFinder;
        $format         = $options['format'] ?? 'txt';
        $hideProgress   = $options['hideProgress'] ?? false;
        $indent         = $options['indent'] ?? '    ';
        $lineEnding     = $options['lineEnding'] ?? "\n";
        $phpExecutable  = $options['phpExecutable'] ?? null;
        $isRiskyAllowed = $options['isRiskyAllowed'] ?? ($ruleset->willAutoActivateIsRiskyAllowed() ?: false);
        $usingCache     = $options['usingCache'] ?? true;

        // Get rules from registered custom fixers, if any
        $customFixerRules = $options['customRules'] ?? [];

        return (new Config($ruleset->getName()))
            ->setCacheFile($cacheFile)
            ->registerCustomFixers($customFixers)
            ->setFinder($finder)
            ->setFormat($format)
            ->setHideProgress($hideProgress)
            ->setIndent($indent)
            ->setLineEnding($lineEnding)
            ->setPhpExecutable($phpExecutable)
            ->setRiskyAllowed($isRiskyAllowed)
            ->setUsingCache($usingCache)
            ->setRules(array_merge($ruleset->getRules(), $overrides, $customFixerRules))
        ;
    }
}
