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

use Liaison\CS\Config\Ruleset\Liaison;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @small
 */
final class BaseRulesetTest extends TestCase
{
    public function testRulesetHierarchy()
    {
        $ruleset = new Liaison();

        $this->assertInstanceOf('Liaison\CS\Config\Ruleset\BaseRuleset', $ruleset);
        $this->assertInstanceOf('Liaison\CS\Config\Ruleset\RulesetInterface', $ruleset);
    }

    public function testRulesetGivesCorrectPropertyTypes()
    {
        $ruleset = new Liaison();

        $this->assertIsString($ruleset->getName());
        $this->assertIsArray($ruleset->getRules());
        $this->assertIsInt($ruleset->getRequiredPHPVersion());
        $this->assertIsBool($ruleset->isRiskyRuleset());
    }

    public function testRulesetAddsHeaderCommentWhenSupplied()
    {
        $header = <<<'EOD'
            This file is part of Liaison CS Config Factory.

            (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>

            For the full copyright and license information, please view the LICENSE
            file that was distributed with this source code.

            EOD;

        $ruleset = new Liaison($header);

        $this->assertArrayHasKey('header_comment', $ruleset->getRules());
        $this->assertSame([
            'header'       => trim($header),
            'comment_type' => 'PHPDoc',
        ], $ruleset->getRules()['header_comment']);
    }
}
