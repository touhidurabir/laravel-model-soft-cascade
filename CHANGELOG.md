# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
- Laravel 11, 12 and 13 support added.
- PHPUnit 10, 11 and 12 support for tests; test methods now use `#[Test]` attributes alongside the `@test` annotations.
- Fixed `LogicException` on recent Laravel versions caused by instantiating the model while it was being booted in `HasSoftCascade::bootHasSoftCascade`.
- Fixed PHP 8.4 implicit nullable parameter deprecations in `SoftCascadeDelete::deleteModelRelations` and `SoftCascadeRestore::restoreModelRelations`.

## [1.0.0] - 2021-10-13
- Initial release

## [1.0.1] - 2021-10-14
- Bug fix.
- Readme update.