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

## Branch protection

Protect `main` and require changes to arrive through pull requests. Direct pushes and force pushes should remain disabled.

Required status checks should include:

- `Quality gates`
- `Release package`
- every supported `PHP lint` matrix job
- every supported WordPress integration matrix job

Require branches to be up to date before merging and dismiss stale approvals when new commits are pushed. Repository administrators should follow the same merge gates except for documented emergency recovery.

## Releases

Release tags use the form `vX.Y.Z`. The plugin header version, `Mivama_Media_Folders::VERSION`, and the `readme.txt` stable tag must match before a release can be built.

Tags matching `v*` trigger the release workflow. That workflow reruns quality checks and integration tests, verifies that the tag equals the plugin version, builds the installable ZIP, and only then publishes the GitHub Release artifact.

## Security releases

Security fixes follow `SECURITY.md`. Keep vulnerability details private until a fix or coordinated disclosure plan is ready, run the normal quality/release gates, and publish a patched version through the same tagged release workflow.
