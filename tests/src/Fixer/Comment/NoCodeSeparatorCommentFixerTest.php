<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Tests\Fixer\Comment;

use Liaison\CS\Config\Test\AbstractCustomFixerTestCase;

/**
 * @internal
 */
final class NoCodeSeparatorCommentFixerTest extends AbstractCustomFixerTestCase
{
    public static function provideFixCases(): iterable
    {
        yield ["<?php\n\$a;\n\n\$b;"];

        yield ['<?php $a; // a comment'];

        yield [
            <<<'EOD'
                <?php

                $a;

                $b;
                EOD,
            <<<'EOD'
                <?php

                $a;

                //-------------------------

                $b;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                $a;

                $b;
                EOD,
            <<<'EOD'
                <?php

                $a;

                // ------------------------

                $b;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                class Foo
                {
                    public function a() {}

                    public function b() {}

                    //---------------------
                    // C
                    //---------------------

                    public function c() {}
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    public function a() {}

                    //---------------------

                    public function b() {}

                    //---------------------
                    // C
                    //---------------------

                    public function c() {}
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     *
     * @return void
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }
}
