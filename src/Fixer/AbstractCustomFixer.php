<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Utils;

/**
 * @internal
 */
abstract class AbstractCustomFixer extends AbstractFixer
{
    final public static function name(): string
    {
        $namespaces    = explode('\\', __NAMESPACE__);
        $rootNamespace = reset($namespaces);

        $nameParts = explode('\\', static::class);
        $name      = mb_substr(end($nameParts), 0, -5);

        return $rootNamespace . '/' . Utils::camelCaseToUnderscore($name);
    }

    final public function getName(): string
    {
        return self::name();
    }
}
