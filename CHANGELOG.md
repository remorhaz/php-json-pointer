# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.7.0] - 2023-09-19
### Removed
- Dropped PHP 7 support.

## [0.6.10] - 2021-01-14
### Added
- PHP 8.0 support.
- UniLex version upgraded to 0.4.0.

## [0.6.9] - 2020-04-12
### Added
- UniLex version updated to 0.3.0.

## [0.6.8] - 2020-04-03
### Added
- UniLex version updated to 0.2.0.

## [0.6.7] - 2020-02-17
### Added
- UniLex version updated to 0.1.0.

## [0.6.6] - 2019-12-04
### Fixed
- Phing dependency moved to `required-dev`.

## [0.6.5] - 2019-11-20
### Added
- Issue #6: locator builder supports export to string.

## [0.6.4] - 2019-11-20
### Added
- Issue #5: `ResultInterface::get()` method added.
### Fixed
- Issue #6: locator builder moved to correct namespace.

## [0.6.3] - 2019-11-18
### Fixed
- Issue #4: replacing document root with `add()` fixed.

## [0.6.2] - 2019-11-18
### Fixed
- Issue #3: adding element to empty array fixed.

## [0.6.1] - 2019-11-18
### Fixed
- All query exceptions have `getSource()` method now.
- Issue #2: delete mutation breaks array indexes. 

## [0.6.0] - 2019-11-05
### Added
- Implementation totally refactored.
