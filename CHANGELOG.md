# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
- Laravel 11, 12 and 13 support added.
- PHPUnit 10, 11 and 12 support for tests; test methods now use `#[Test]` attributes alongside the `@test` annotations.
- Fixed `LogicException` on recent Laravel versions caused by instantiating the model while it was being booted in `HasSoftCascade::bootHasSoftCascade`.
- Fixed PHP 8.4 implicit nullable parameter deprecations in `SoftCascadeDelete::deleteModelRelations` and `SoftCascadeRestore::restoreModelRelations`.
- Code style formatted to PSR-12 with Laravel Pint (`composer format` / `composer lint`).
- Replaced Travis CI with GitHub Actions (PHP 8.2–8.4 × Laravel 11/12/13, plus a code style check).
- Fixed a force delete leaking into later soft deletes. After any model force deleted its relations, a plain `delete()` of another model of the same class also force deleted its relations. **Behaviour change:** a soft delete now always soft deletes the relations, as documented.
- Fixed the cascade transaction being left open when a relation fails to delete or restore. The cascade now runs in `DB::transaction()`, so it is rolled back and the exception is rethrown.
- Fixed `deleteModelRelationRecord()`, which read the delete method as a property instead of calling it, so it never deleted anything.
- Fixed a `TypeError` when `cascadable()` configures only `delete` or only `restore`. The missing action no longer cascades, as the README describes.


## [1.0.0] - 2021-10-13
- Initial release

## [1.0.1] - 2021-10-14
- Bug fix.
- Readme update.