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

Build an installable plugin archive with:

```bash
bash bin/build-release.sh
```

The generated archive is `dist/mivama-media-folders.zip`. Release tags use `vX.Y.Z`; the tag, plugin header, class version constant and WordPress `Stable tag` must remain consistent. Tagged releases are verified, tested, packaged and published through GitHub Actions.

## Security

See `SECURITY.md` for vulnerability reporting guidance.

## Contributing

See `CONTRIBUTING.md` before opening a pull request.

## License

GPL-2.0-or-later.
