<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
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
    private function __construct()
    {
        // Nothing to instantiate.
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
        if (PHP_VERSION_ID < $ruleset->getRequiredPHPVersion()) {
            throw new RuntimeException(sprintf(
                'Ruleset "%s" requires a minimum PHP version ID "%d" but current PHP version ID is "%d".',
                $ruleset->getName(),
                $ruleset->getRequiredPHPVersion(),
                PHP_VERSION_ID
            ));
        }

        // Resolve Config options
        $cacheFile      = $options['cacheFile']      ?? '.php_cs.cache';
        $customFixers   = $options['customFixers']   ?? [];
        $finder         = $options['finder']         ?? new Finder();
        $format         = $options['format']         ?? 'txt';
        $hideProgress   = $options['hideProgress']   ?? false;
        $indent         = $options['indent']         ?? '    ';
        $lineEnding     = $options['lineEnding']     ?? "\n";
        $phpExecutable  = $options['phpExecutable']  ?? null;
        $isRiskyAllowed = $options['isRiskyAllowed'] ?? ($ruleset->isRiskyRuleset() ?: false);
        $usingCache     = $options['usingCache']     ?? true;
        $rules          = array_merge($ruleset->getRules(), $overrides);

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
            ->setRules($rules)
        ;
    }
}
