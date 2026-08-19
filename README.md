# Mivama Media Folders

Mivama Media Folders adds folder management to the native WordPress Media Library without moving files or changing media URLs.

## Features

- Folder management for the native Media Library
- Nested folders
- Attachment assignment from media details
- List and grid filtering
- Bulk move/remove actions
- Dedicated folder-management capability
- Non-destructive taxonomy-based organization

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer

The current `1.0.0` release candidate is explicitly tested through WordPress 7.1.

## Development

```bash
composer install
composer check
```

Build the installable distribution with:

```bash
bash bin/build-release.sh
```

The generated ZIP is written to `dist/mivama-media-folders.zip`. Development-only files, release testing documentation and WordPress.org directory assets are excluded from that package.

## Testing

Pull requests run PHP linting, WordPress Coding Standards, PHP compatibility checks, version consistency, WordPress Plugin Check, release-package validation, checksum verification, clean ZIP installation/activation and the supported WordPress integration matrix.

Before publishing a release, complete the repository's `TESTING.md` checklist against the exact generated ZIP.

## WordPress.org assets

Directory-only assets are staged under `wordpress-org/` and intentionally excluded from the installable plugin ZIP. This directory is reserved for the WordPress.org icon, banners and screenshots; it is not runtime plugin code.

## Releases

The first public version is `1.0.0`. Later releases follow semantic versioning.

Prepare a release version on a normal branch:

```bash
php bin/bump-version.php 1.0.0
composer version
bash bin/build-release.sh
```

Once automated gates and the manual checklist pass, run **Actions → Release → Run workflow** from `main` with the version without the `v` prefix. The workflow repeats the release gates, builds the ZIP, verifies its SHA-256 checksum, creates the version tag and publishes the GitHub Release.

Merging code does not itself publish a release.

## License

GPL-2.0-or-later.
