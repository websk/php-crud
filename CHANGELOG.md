# Changelog

## [4.0.0](https://github.com/websk/php-crud/releases/tag/4.0.0) - 2026-10-24

### Added

- ESLint checks for the asset manifest, Playwright regressions for the documented frontend behavior baseline, and npm commands for asset reproducibility and dependency audits.
- A Docker demo browser check for real CRUD create/edit forms, Magnific Popup, Ace synchronization and remote Tokenize2 filtering.

### Changed

- Replaced the copy-only Webpack setup with a deterministic Node.js 24 asset build and reproducibility check.
- Updated jQuery, jQuery UI, jQuery Validation, Ace and Font Awesome; legacy Font Awesome class names remain available through the official compatibility shim.
- Updated Bootstrap from 3.4.1 to 5.3.8 and migrated CRUD forms, controls, pagination, modals, collapse triggers and responsive utilities to Bootstrap 5 markup.
- Replaced external MediumEditor and Blueimp File Upload CDN references with npm-managed local assets and added Tokenize2 1.3.5 as a direct dependency of the deterministic asset build.
- Replaced Eonasdan Bootstrap Datetimepicker and Moment with native browser controls while preserving SQL date-time and Unix timestamp form values.
- Applied native required-field validation to the visible date-time and timestamp controls.
- Used the Ace text editor for comments in the demo user create and edit forms.
- Corrected generated HTML5 markup, completed the Bootstrap 5 utility migration, improved responsive form and table layouts and control contrast, and moved shared CRUD widget styles into the deterministic asset build.
- Removed the unused GPL-licensed Fancybox dependency and its demo layout assets.
- Removed the obsolete Webpack configuration and unused PHP 8.3 ini file from the release archive.

## [3.0.0](https://github.com/websk/php-crud/releases/tag/3.0.0) - 2026-10-12

### Changed

- Added PHP 8.5 runtime and Docker support while retaining PHP 8.3 as the minimum version.
- Upgraded the application stack to PHP-DI 7, Slim 4.15, `websk/php-config` 2.2,
  `websk/php-utils` 3.0 and `websk/php-view` 3.1.
- Replaced deprecated request decorators and annotation injection with PSR-7 request access and PHP attributes.
- Added strict request-boundary validation and PHP 8.5-safe scalar, nullable and reflection handling.
- Added PHPUnit 12 and PHPStan 2 at level `max` to the project checks.

### Added

- Added request parameter, pager and PHP-DI container integration tests.
- Added an adapter for mutable WebSK service-provider registration with PHP-DI 7.
- Added the MIT license.

Historical entries below were reconstructed from GitHub release dates, tags and
commit history because the published releases do not contain release notes.

## [2.3.1](https://github.com/websk/php-crud/releases/tag/2.3.1) - 2025-11-25

### Fixed

- Fixed uploaded-file deletion.

## [2.3.0](https://github.com/websk/php-crud/releases/tag/2.3.0) - 2025-03-26

### Changed

- Updated package dependencies.

## [2.2.0](https://github.com/websk/php-crud/releases/tag/2.2.0) - 2025-03-25

### Changed

- Updated package dependencies.

## [2.1.1](https://github.com/websk/php-crud/releases/tag/2.1.1) - 2025-03-25

### Fixed

- Replaced implicitly nullable declarations with explicit nullable types.

## [2.1.0](https://github.com/websk/php-crud/releases/tag/2.1.0) - 2025-03-23

### Changed

- Updated the Slim invocation strategy integration.
- Updated the README.

## [2.0.0](https://github.com/websk/php-crud/releases/tag/2.0.0) - 2025-02-09

### Changed

- Raised the minimum PHP version to PHP 8.3.
- Updated package dependencies for the PHP 8.3 stack.

## [1.4.1](https://github.com/websk/php-crud/releases/tag/1.4.1) - 2022-02-13

### Changed

- Updated Composer dependencies.

## [1.3.6](https://github.com/websk/php-crud/releases/tag/1.3.6) - 2022-02-05

### Fixed

- Fixed handling of boolean options.

## [1.3.5](https://github.com/websk/php-crud/releases/tag/1.3.5) - 2021-11-05

### Changed

- Applied general package updates.

## [1.3.4](https://github.com/websk/php-crud/releases/tag/1.3.4) - 2021-07-21

### Changed

- Migrated request and response usage to PSR interfaces.

## [1.3.3](https://github.com/websk/php-crud/releases/tag/1.3.3) - 2021-06-22

### Fixed

- Allowed a null editor URL in `CRUDFormWidgetReferenceAjax`.

## [1.3.2](https://github.com/websk/php-crud/releases/tag/1.3.2) - 2021-06-08

### Fixed

- Fixed component styles.

## [1.3.1](https://github.com/websk/php-crud/releases/tag/1.3.1) - 2021-06-04

### Added

- Added new CRUD elements.

## [1.3.0](https://github.com/websk/php-crud/releases/tag/1.3.0) - 2021-04-14

### Changed

- Updated Composer dependencies.

## [1.2.2](https://github.com/websk/php-crud/releases/tag/1.2.2) - 2021-02-07

### Changed

- Updated the demo and README.
- Removed the obsolete example configuration file.

## [1.2.1](https://github.com/websk/php-crud/releases/tag/1.2.1) - 2021-02-05

### Changed

- Updated the package for PHP 7.4.

### Fixed

- Cleared the entity file field after deleting an uploaded file.

## [1.1.8](https://github.com/websk/php-crud/tree/1.1.8) - 2021-02-05

Tag-only release pointing to the same package state as 1.2.1.

## [1.1.7](https://github.com/websk/php-crud/releases/tag/1.1.7) - 2020-03-23

### Added

- Added `save_as` support to the upload-file widget.

## [1.1.6](https://github.com/websk/php-crud/releases/tag/1.1.6) - 2020-03-01

### Fixed

- Fixed form request handling.

## [1.1.5](https://github.com/websk/php-crud/tree/1.1.5) - 2020-02-26

### Fixed

- Fixed type declarations.

## [1.1.4](https://github.com/websk/php-crud/releases/tag/1.1.4) - 2020-02-25

### Added

- Added storage-backed file upload support.

## [1.1.3](https://github.com/websk/php-crud/releases/tag/1.1.3) - 2020-02-02

### Added

- Added the CRUD file-upload widget.

## [1.1.2](https://github.com/websk/php-crud/tree/1.1.2) - 2020-01-29

### Changed

- Updated the CRUD file-upload widget implementation.

## [1.1.1](https://github.com/websk/php-crud/tree/1.1.1) - 2019-12-19

### Removed

- Removed `CRUDController`.

## [1.1.0](https://github.com/websk/php-crud/releases/tag/1.1.0) - 2019-07-17

### Changed

- Refactored CRUD internals.
- Added and updated the demo CRUD application.

## [1.0.9](https://github.com/websk/php-crud/releases/tag/1.0.9) - 2019-07-12

### Added

- Added flash-message support.
- Added `CRUDFormWidgetAceTextarea`.

## [1.0.8](https://github.com/websk/php-crud/releases/tag/1.0.8) - 2019-07-04

### Changed

- Refactored CRUD code and the delete action.

## [1.0.7](https://github.com/websk/php-crud/releases/tag/1.0.7) - 2019-05-16

### Fixed

- Updated component styles.

## [1.0.6](https://github.com/websk/php-crud/releases/tag/1.0.6) - 2019-04-24

### Fixed

- Updated component styles.

## [1.0.5](https://github.com/websk/php-crud/releases/tag/1.0.5) - 2019-04-24

### Fixed

- Updated component styles.

## [1.0.4](https://github.com/websk/php-crud/releases/tag/1.0.4) - 2019-04-15

### Changed

- Updated Composer metadata and dependencies.

## [1.0.3](https://github.com/websk/php-crud/releases/tag/1.0.3) - 2019-04-15

### Fixed

- Fixed modal behavior.

## [1.0.2](https://github.com/websk/php-crud/releases/tag/1.0.2) - 2019-01-15

### Removed

- Removed obsolete scripts.

## [1.0.1](https://github.com/websk/php-crud/releases/tag/1.0.1) - 2018-12-12

### Changed

- Updated Composer metadata and dependencies.

## [1.0.0](https://github.com/websk/php-crud/releases/tag/1.0.0) - 2018-12-12

### Added

- Published the first stable CRUD package release.
- Added entity-based CRUD operations and object-ID value handling.
