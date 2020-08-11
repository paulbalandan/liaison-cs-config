# Liaison CS Config Factory

![build](https://github.com/paulbalandan/liaison-cs-config/workflows/build/badge.svg?branch=develop)
[![license MIT](https://img.shields.io/github/license/paulbalandan/cs-config)](LICENSE)

This library provides a configuration factory for custom rule sets
for [`friendsofphp/php-cs-fixer`](http://github.com/FriendsOfPHP/PHP-CS-Fixer).

## Installation

```bash
composer require --dev liaison/cs-config
```

## Getting Started

### Configuration

* Choose one of the configured rulesets:
  - [`Liaison\CS\Config\Ruleset\Liaison`](src/Ruleset/Liaison.php)
  - `Liaison\CS\Config\Ruleset\CodeIgniter4` **(INCOMING)**
  - `Liaison\CS\Config\Ruleset\Laravel7` **(INCOMING)**

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

+ # php-cs-fixer
+ .php_cs
+ .php_cs.cache
```

### Advanced Configuration

* Adding a header comment
```diff
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

+ $header = <<<EOD
+ This file is part of Liaison CS Config Factory.
+
+ (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
+
+ For the full copyright and license information, please view the LICENSE
+ file that was distributed with this source code.
+ EOD;

- return Factory::create(new Liaison());
+ return Factory::create(new Liaison($header));

```

This will enable and configure the `HeaderCommentFixer` so that file headers will be added to PHP files. For example:

```php
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
```

* Overriding rules in the ruleset

If you feel that a specific rule in the ruleset is not appropriate for you, you can override it instead of creating a new ruleset:

```diff
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

- return Factory::create(new Liaison());
+ return Factory::create(new Liaison(), [
+     'binary_operator_spaces' => false,
+ ]);

```

* Specifying options to `PhpCsFixer\Config`

The `Factory` returns an instance of `PhpCsFixer\Config` and fully supports all of
its properties setup. You can pass an array to the third parameter of `Factory::create()`
containing your desired options.

**Options**

| Key            | Allowed Types (from `ConfigInterface`)   | Default                              |
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

```diff
<?php

use Liaison\CS\Config\Factory;
use Liaison\CS\Config\Ruleset\Liaison;

- return Factory::create(new Liaison());
+ return Factory::create(new Liaison(), [], [
+     'usingCache'  => false,
+     'hideProgress => true,
+ ]);
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
   * Does this ruleset have risky rules? If yes and
   * PhpCsFixer\Config has the `$isRiskyAllowed` set to
   * `false`, those risky rules won't be run.
   *
   * Set this flag to `true` to automatically setup
   * the `$isRiskyAllowed` flag.
   *
   * @var bool
   */
  protected $isRisky = false;
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
