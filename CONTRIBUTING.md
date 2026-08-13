# Contributing

## Development setup

```bash
composer install
```

For integration tests, create a local MySQL/MariaDB test database and run:

```bash
bash bin/install-wp-tests.sh wordpress_test root root 127.0.0.1 7.0.2
composer test
```

## Quality checks

Run before opening a pull request:

```bash
composer check
```

Individual checks are available through `composer lint`, `composer phpcs`, `composer compat`, `composer version`, and `composer test`.

## Pull requests

Keep changes focused, add or update tests for behavior changes, update the changelog for user-facing changes, and do not commit `vendor/` or generated release archives.

## Coding standards

PHP follows the WordPress Coding Standards and the plugin retains compatibility with its declared minimum PHP version.

## Releases

Release tags use the form `vX.Y.Z`. The plugin header version, `Mivama_Media_Folders::VERSION`, and the `readme.txt` stable tag must match before a release can be built.
