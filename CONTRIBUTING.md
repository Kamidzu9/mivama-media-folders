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
bash bin/build-release.sh
```

Individual checks are available through `composer lint`, `composer phpcs`, `composer compat`, `composer version`, and `composer test`.

Pull-request CI additionally validates the release ZIP checksum, runs WordPress Plugin Check against the built distribution, and installs/activates that ZIP on a clean WordPress instance.

## Pull requests

Keep changes focused, add or update tests for behavior changes, update the changelog for user-facing changes, and do not commit `vendor/` or generated release archives.

## Coding standards

PHP follows the WordPress Coding Standards and the plugin retains compatibility with its declared minimum PHP version.

## Branch protection

Protect `main` and require changes to arrive through pull requests. Direct pushes and force pushes should remain disabled.

Required status checks should include:

- `Quality gates`
- `Release package`
- `WordPress Plugin Check`
- `Release ZIP smoke test`
- every supported `PHP lint` matrix job
- every supported WordPress integration matrix job

Require branches to be up to date before merging and dismiss stale approvals when new commits are pushed. Repository administrators should follow the same merge gates except for documented emergency recovery.

## Releases

Release tags use the form `vX.Y.Z`. The plugin header version, `Mivama_Media_Folders::VERSION`, and the `readme.txt` stable tag must match before a release can be built.

Prepare a release version on a normal branch:

```bash
php bin/bump-version.php 1.5.0
composer version
bash bin/build-release.sh
```

Merge the version change only after all required checks are green. Then run **Actions → Release → Run workflow** from `main` and enter the same version without the `v` prefix.

The release workflow repeats quality checks and integration tests, runs WordPress Plugin Check against the built distribution, installs and activates the generated ZIP on clean WordPress, verifies the SHA-256 checksum, and only then publishes the GitHub Release.

## Release rollback

Git tags and published release artifacts are immutable release evidence and must not be silently replaced.

If a release is bad:

1. Do not rebuild or overwrite the existing release ZIP under the same version.
2. Mark the affected GitHub Release as problematic in its release notes when appropriate.
3. Revert or fix the faulty change through a normal pull request.
4. Prepare a new patch version with `php bin/bump-version.php X.Y.Z`.
5. Require the complete CI and release verification gates again.
6. Publish the corrected patch as a new tag and GitHub Release.
7. For WordPress.org, deploy the corrected patch version rather than modifying the contents of an already published version tag.

This keeps GitHub and WordPress.org release history auditable and ensures every published version maps to one reproducible artifact.

## Security releases

Security fixes follow `SECURITY.md`. Keep vulnerability details private until a fix or coordinated disclosure plan is ready, run the normal quality/release gates, and publish a patched version through the same verified release workflow.
