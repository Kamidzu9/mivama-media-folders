# Mivama Media Folders

A lightweight WordPress plugin that adds hierarchical folders to the native Media Library without physically moving uploaded files or changing their URLs.

## Features

- Create, edit and delete media folders
- Nested folder hierarchy
- Assign attachments to folders
- Folder filters in list and grid views
- Bulk move and remove-from-folder actions
- Unassigned media filter
- Native WordPress admin integration

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Installation

1. Download or clone this repository.
2. Copy the plugin directory to `wp-content/plugins/mivama-media-folders` or install a release ZIP from WordPress Admin.
3. Activate **Mivama Media Folders**.
4. Open **Media → Folders**.

## How it works

Folders are stored as a private hierarchical taxonomy attached to media items. The plugin does **not** move physical files, so existing media URLs remain stable.

## Development

Install development dependencies:

```bash
composer install
```

Run the local quality gate:

```bash
composer check
```

The repository checks PHP syntax, WordPress coding standards, PHP 7.4+ compatibility, version consistency and PHPUnit integration tests.

### WordPress integration tests

Install a WordPress test suite and run PHPUnit:

```bash
bash bin/install-wp-tests.sh wordpress_test root root 127.0.0.1 7.0.2
composer test
```

GitHub Actions tests the minimum supported WordPress 6.0 branch and the current stable WordPress branch across representative PHP versions.

## Releases

Prepare a new version on a release branch:

```bash
php bin/bump-version.php 1.5.0
composer version
bash bin/build-release.sh
```

The bump helper updates the plugin header, `Mivama_Media_Folders::VERSION`, the WordPress `Stable tag` and the changelog heading together. Commit those changes through a normal pull request and merge only after CI is green.

After the version bump is merged to `main`, open **Actions → Release → Run workflow**, select `main`, and enter the version without the `v` prefix. The release workflow then:

1. verifies that the requested version matches every version declaration,
2. reruns linting, coding standards, PHP compatibility and integration tests,
3. builds the installable ZIP,
4. generates a SHA-256 checksum,
5. creates the `vX.Y.Z` tag, and
6. publishes the GitHub Release with the ZIP and checksum.

Pushing an existing `vX.Y.Z` tag remains supported and goes through the same verification and packaging gates.

## Security

See `SECURITY.md` for vulnerability reporting guidance.

## Contributing

See `CONTRIBUTING.md` before opening a pull request.

## License

GPL-2.0-or-later.
