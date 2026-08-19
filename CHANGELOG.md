# Changelog

All notable public changes to Mivama Media Folders are documented here.

## [Unreleased]

## [1.0.0] - 2026-08-19

### Added
- Initial public release of Mivama Media Folders.
- Native WordPress Media Library folder management backed by a private hierarchical attachment taxonomy.
- Folder creation, editing, deletion and nested folder support.
- Attachment assignment from media details plus list/grid filtering and bulk move/remove actions.
- Dedicated `manage_media_folders` capability for structural folder management.
- Authorization, nonce, invalid-input and folder CRUD regression coverage.
- Reproducible Composer dependency locking and WordPress PHPUnit Polyfills support.
- WordPress integration tests across the supported PHP/WordPress compatibility matrix.
- Pull-request release ZIP validation that rejects development-only files.
- WordPress Plugin Check against the built release distribution.
- Clean WordPress install and activation smoke testing for the generated release ZIP.
- Synchronized release-version preparation and verified SHA-256 checksums.
- Manual GitHub Actions release workflow with version verification and immutable release artifacts.
- WordPress.org FAQ covering URL stability, folder deletion, nesting, permissions and uninstall data retention.

### Changed
- Structural folder creation, editing and deletion are separated from attachment assignment permissions.
- WordPress compatibility metadata reflects the tested WordPress 7.0 target while retaining WordPress 6.0 as the declared minimum.
- Release artifacts use short retention to reduce unnecessary Actions storage usage.
- Routine Composer development and GitHub Actions minor/patch dependency updates are grouped to reduce maintenance noise.

### Fixed
- Numeric folder IDs are handled as existing terms instead of being interpreted as new folder names.
- Attachment assignment uses a dedicated internal field key and integer term IDs.
- WordPress 7 test bootstrap configuration and duplicate test constant warnings.
- Version consistency checks compare release metadata without hard-coded release versions.
- WordPress Coding Standards and PHP 7.4+ compatibility issues found during release hardening.
- Main plugin metadata declares minimum WordPress and PHP versions directly in the plugin header.
