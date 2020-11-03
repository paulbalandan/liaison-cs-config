# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.2.1](https://github.com/paulbalandan/liaison-cs-config/compare/v1.2.0...v1.2.1) - 2020-11-03

### Fixed

- Code separators used as comment block boundaries are no longer removed by `NoCodeSeparatorCommentFixer`

## [v1.2.0](https://github.com/paulbalandan/liaison-cs-config/compare/v1.1.5...v1.2.0) - 2020-11-02

### Added

- Add starting year of copyright at LICENSE and file headers
- Add new static method for Factory: `createForLibrary`
- Add custom fixer `NoCodeSeparatorCommentFixer`
- Add new option key to accept custom rules from custom fixers
- Add PHP version badge to README

### Changed

- Bump PHP version requirement to PHP 7.3
- Bump `Liaison` ruleset's minimum required PHP version ID to `703200`
- Updated installation instructions in README
- Restricts Finder to files only
- Return types of test classes' test methods are added explicitly as `void`
- Refactored abstract test cases into `src/`
- Updated rules of rulesets and declared them as "final" classes

### Fixed

- Make the default resolution of path by `Finder` safe from `realpath`'s falsy return

### Deprecated

- Deprecated `BaseRuleset` in favor of `AbstractRuleset`

## [v1.1.5](https://github.com/paulbalandan/liaison-cs-config/compare/v1.1.4...v1.1.5) - 2020-10-11

### Added

- Added `php-coveralls` to badge and build workflow

### Changed

- Updates to coding style rules for `CodeIgniter4` ruleset
- `composer.json` now includes config `preferred-install: dist`
- Build workflow now uses Composer v2

## [v1.1.4](https:://github.com/paulbalandan/liaison-cs-config/compare/v1.1.3...v1.1.4) - 2020-08-31

### Fixed

- Fixed forgotten `realpath` on directory resolution of Finder

## [v1.1.3](https://github.com/paulbalandan/liaison-cs-config/compare/v1.1.2...v1.1.3) - 2020-08-31

### Fixed

- Fixed changelog with regard to release date of previous version release. No changes to source code.

## [v1.1.2](https://github.com/paulbalandan/liaison-cs-config/compare/v1.1.1...v1.1.2) - 2020-08-31

### Added

- Added PHPStan as development dependency.

### Changed

- Turned off the following fixers: `phpdoc_no_empty_return`, `self_accessor`

### Fixed

- Excluded `build` directory in location where `Finder` will look for PHP files.

## [v1.1.1](https://github.com/paulbalandan/liaison-cs-config/compare/v1.1.0...v1.1.1) - 2020-08-22

### Fixed

- Revert `escape_implicit_backslashes` to use default value of `false` for `single_quoted` option.

## [v1.1.0](https://github.com/paulbalandan/liaison-cs-config/compare/v1.0.3...v1.1.0) - 2020-08-20

### Added

- New `CodeIgniter4` ruleset.

### Changed

- Code style changes to `Liaison` ruleset.
- Updated composer/xdebug-handler to 1.4.3

### Fixed

- Path used in default `PhpCSFixer\Finder`'s instance is now passed to `realpath` to resolve relative links.

## [v1.0.3](https://github.com/paulbalandan/liaison-cs-config/compare/v1.0.2...v1.0.3) - 2020-08-15

### Added

- Add missing rules on `Liaison` ruleset hidden by `@PSR2` ruleset

### Changed

- Updated composer dependencies

### Fixed

- Heredoc rules conflicting with PHP 7.2

## [v1.0.2](https://github.com/paulbalandan/liaison-cs-config/compare/v1.0.1...v1.0.2) - 2020-08-13

### Changed

- Downgraded dependencies to still support PHP 7.2. Make the version bump on EOL.

## [v1.0.1](https://github.com/paulbalandan/liaison-cs-config/compare/v1.0.0...v1.0.1) - 2020-08-11

### Fixed

- Fixed wrong `Finder`'s default file location when installed in `vendor/`.

## v1.0.0 - 2020-08-11

### Added

- Initial release
