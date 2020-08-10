<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Ruleset;

interface RulesetInterface
{
    /**
     * Name of this ruleset.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Defined rules for this ruleset.
     *
     * @return array
     */
    public function getRules(): array;

    /**
     * Returns the minimum `PHP_VERSION_ID`
     * that is required by this ruleset.
     *
     * @return int
     */
    public function getRequiredPHPVersion(): int;

    /**
     * Does this ruleset have risky rules?
     *
     * If yes and `PhpCsFixer\Config` has the `$isRiskyAllowed`
     * flag set to `false`, those risky rules won't be run.
     *
     * Set this flag to `true` to automatically setup
     * the `$isRiskyAllowed` flag.
     *
     * @return bool
     */
    public function isRiskyRuleset(): bool;
}
