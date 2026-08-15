# Changelog

All notable changes to Mivama Media Folders are documented here.

## [Unreleased]

### Added
- Standalone repository quality, testing, CI and release foundation.
- Reproducible Composer dependency locking and WordPress PHPUnit Polyfills support.
- Real WordPress integration coverage across the supported PHP matrix.
- Dedicated `manage_media_folders` capability for structural folder management.
- Folder CRUD, permission, AJAX authorization, nonce and invalid-input regression coverage.
- Pull-request release ZIP validation that rejects development-only files.

### Changed
- Structural folder creation, editing and deletion are separated from attachment assignment permissions.
- WordPress compatibility metadata now reflects the WordPress 7.0 integration target.
- Contributor guidance documents branch protection and release/security gates.

### Fixed
- WordPress 7 test bootstrap configuration and duplicate test constant warnings.
- Version consistency checking with formatted class constants.
- WordPress Coding Standards issues in production and integration-test code.

## [1.4.4]
- Refactored the plugin into smaller include files.
- Loaded media folder controls across WordPress admin surfaces where the media modal can appear.

## [1.3.0]
- Fixed numeric folder IDs being treated as new folder names.
- Added a dedicated internal attachment field key.
- Improved existing-folder validation.

## [1.2.0]
- Rebuilt the Media > Folders page.
- Added folder edit/delete table, AJAX creation, filtering and bulk move support.

## [1.1.0]
- Added inline new-folder modal.

## [1.0.0]
- Initial release.
