<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Ruleset;

abstract class AbstractRuleset implements RulesetInterface
{
    /**
     * Name of the ruleset.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Rules for the ruleset.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Minimum PHP version.
     *
     * @var int
     */
    protected $requiredPHPVersion = 0;

    /**
     * Have this ruleset turn on `$isRiskyAllowed` flag?
     *
     * @var bool
     */
    protected $autoActivateIsRiskyAllowed = false;

    /**
     * Constructor.
     *
     * @param null|string $header header to include
     */
    final public function __construct(?string $header = null)
    {
        if (null !== $header) {
            $this->rules['header_comment'] = [
                'header'       => trim($header),
                'comment_type' => 'PHPDoc',
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPHPVersion(): int
    {
        return $this->requiredPHPVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function willAutoActivateIsRiskyAllowed(): bool
    {
        return $this->autoActivateIsRiskyAllowed;
    }
}
