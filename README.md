# Liaison CS Config Factory

![build](https://github.com/paulbalandan/liaison-cs-config/workflows/build/badge.svg?branch=develop)
[![PHP version](https://img.shields.io/packagist/php-v/liaison/cs-config)](https://php.net)
[![Coverage Status](https://coveralls.io/repos/github/paulbalandan/liaison-cs-config/badge.svg?branch=develop)](https://coveralls.io/github/paulbalandan/liaison-cs-config?branch=develop)
[![license MIT](https://img.shields.io/github/license/paulbalandan/cs-config)](LICENSE)
[![Latest Stable Version](https://poser.pugx.org/liaison/cs-config/v)](//packagist.org/packages/liaison/cs-config)
[![Latest Unstable Version](https://poser.pugx.org/liaison/cs-config/v/unstable)](//packagist.org/packages/liaison/cs-config)
[![Total Downloads](https://poser.pugx.org/liaison/cs-config/downloads)](//packagist.org/packages/liaison/cs-config)

This library provides a configuration factory for custom rule sets
for [`friendsofphp/php-cs-fixer`](http://github.com/FriendsOfPHP/PHP-CS-Fixer).

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

    composer require liaison/cs-config

If you only need this library during development, for instance to run your project's test suite, then you should add it as a development-time dependency:

    composer require --dev liaison/cs-config

## Getting Started

### Configuration

* Choose one of the configured rulesets:
  - [`Liaison\CS\Config\Ruleset\Liaison`](src/Ruleset/Liaison.php)
  - [`Liaison\CS\Config\Ruleset\CodeIgniter4`](src/Ruleset/CodeIgniter4.php)

* Create a `.php_cs.dist` at the root of your project:

```php
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

return Factory::create(new Liaison());

```

* Include the cache file in your `.gitignore`. By
default, the cache file will be saved in the project root.

```diff
vendor/

+# php-cs-fixer
+.php_cs
+.php_cs.cache
```

### Advanced Configuration

* Adding a header comment
```diff
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

+$header = <<<EOD
+This file is part of Liaison CS Config Factory.
+
+(c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
+
+For the full copyright and license information, please view the LICENSE
+file that was distributed with this source code.
+EOD;

-return Factory::create(new Liaison());
+return Factory::create(new Liaison($header));

```

This will enable and configure the `HeaderCommentFixer` so that file headers will be added to PHP files. For example:

```php
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
```

Alternatively, as of v1.2.0, you can use the `Factory::createForLibrary()` method to add a pre-formatted
license header comment like in above. This static method accepts four required arguments and two optional
arguments in the following order:

- string `$library`
- string `$author`
- int `$initialLicenseYear`
- string `$rulesetName`
- array `$overrides` (default: `[]`)
- array `$options` (default: `[]`)

```diff
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

-return Factory::create(new Liaison());
+return Factory::createForLibrary('My Library', 'John Doe', 2020, Liaison::class);

```

This will create the same file headers as above. To configure a different format for the file header,
you should the first method using `Factory::create()`.

* Overriding rules in the ruleset

If you feel that a specific rule in the ruleset is not appropriate for you, you can override it instead of creating a new ruleset:

```diff
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

-return Factory::create(new Liaison());
+return Factory::create(new Liaison(), [
+    'binary_operator_spaces' => false,
+]);

```

* Specifying options to `PhpCsFixer\Config`

The `Factory` returns an instance of `PhpCsFixer\Config` and fully supports all of
its properties setup. You can pass an array to the third parameter of `Factory::create()`
containing your desired options.

**Options**

| Key            | Allowed Types                            | Default                              |
| -------------- | :--------------------------------------: | :----------------------------------: |
| cacheFile      | `string`                                 | `.php_cs.cache`                      |
| customFixers   | `FixerInterface[], iterable, \Traversable` | `[]`                                 |
| finder         | `iterable, string[], \Traversable`         | default `PhpCsFixer\Finder` instance |
| format         | `string`                                 | `txt`                                |
| hideProgress   | `bool`                                   | `false`                              |
| indent         | `string`                                 | `'    '` // 4 spaces                 |
| lineEnding     | `string`                                 | `"\n"`                               |
| phpExecutable  | `null, string`                           | `null`                               |
| isRiskyAllowed | `bool`                                   | `false`                              |
| usingCache     | `bool`                                   | `true`                               |
| customRules    | `array`                                  | `[]`                                 |

```diff
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

-return Factory::create(new Liaison());
+return Factory::create(new Liaison(), [], [
+    'usingCache'  => false,
+    'hideProgress => true,
+]);
```

## Custom Rulesets

What is the purpose of a configuration factory if not able to create a custom ruleset for
an organization-wide usage, right? Well, you are not constrained to use the default rulesets
and putting a long array of overrides. That's pretty nasty.

The way to achieve this is dependent on you but the main idea is creating a new ruleset that
extends `Liaison\CS\Config\Ruleset\BaseRuleset`. Yup, it's that easy. Then you just need to
provide details for its required four (4) protected properties.

```php
<?php

namespace MyCompany\CodingStandards\Ruleset;

use Liaison\CS\Config\Ruleset\BaseRuleset;

class MyCompany extends BaseRuleset
{
  /**
   * Name of this ruleset
   *
   * @var string
   */
  protected $name = 'My Company';

  /**
   * Your list of rules
   *
   * @var array
   */
  protected $rules = [
    '@PSR2' => true,
    ...
  ];

  /**
   * PHP_VERSION_ID that this ruleset is targeting.
   *
   * @var int
   */
  protected $requiredPHPVersion = 70400;

  /**
   * Does this ruleset have risky rules?
   *
   * If yes and `PhpCsFixer\Config` has the `$isRiskyAllowed`
   * flag set to `false`, those risky rules won't be run.
   *
   * Set this flag to `true` to automatically setup
   * the `$isRiskyAllowed` flag.
   *
   * @var bool
   */
  protected $autoActivateIsRiskyAllowed = false;
}

```

Then, in creating your `.php_cs.dist`, use your own ruleset.

```php
<?php

use Liaison\CS\Config\Factory;
use MyCompany\CodingStandards\Ruleset\MyCompany;

return Factory::create(new MyCompany());

```

## Credits

This project is inspired by and an enhancement of [`ergebnis/php-cs-fixer-config`](https://github.com/ergebnis/php-cs-fixer-config).

## Contributing

Contributions are very much welcome. If you see an improvement or bugfix, open a [PR](https://github.com/paulbalandan/liaison-cs-config/pulls) now!
