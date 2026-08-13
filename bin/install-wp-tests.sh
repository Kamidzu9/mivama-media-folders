#!/usr/bin/env bash
set -euo pipefail

DB_NAME="${1:-wordpress_test}"
DB_USER="${2:-root}"
DB_PASS="${3:-root}"
DB_HOST="${4:-127.0.0.1}"
WP_VERSION="${5:-7.0.2}"

WP_CORE_DIR="${WP_CORE_DIR:-/tmp/wordpress}"
WP_TESTS_DIR="${WP_TESTS_DIR:-/tmp/wordpress-tests-lib}"

rm -rf "${WP_CORE_DIR}" "${WP_TESTS_DIR}"
mkdir -p "${WP_CORE_DIR}" "${WP_TESTS_DIR}"

curl -fsSL "https://wordpress.org/wordpress-${WP_VERSION}.tar.gz" | tar -xz --strip-components=1 -C "${WP_CORE_DIR}"

svn export --quiet --force "https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/" "${WP_TESTS_DIR}/includes"
svn export --quiet --force "https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/data/" "${WP_TESTS_DIR}/data"

cat > "${WP_TESTS_DIR}/wp-tests-config.php" <<PHP
<?php
define( 'DB_NAME', '${DB_NAME}' );
define( 'DB_USER', '${DB_USER}' );
define( 'DB_PASSWORD', '${DB_PASS}' );
define( 'DB_HOST', '${DB_HOST}' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
\$table_prefix = 'wptests_';
define( 'ABSPATH', '${WP_CORE_DIR}/' );
define( 'WP_DEBUG', true );
PHP

mysqladmin --host="${DB_HOST}" --user="${DB_USER}" ${DB_PASS:+--password="${DB_PASS}"} create "${DB_NAME}" 2>/dev/null || true

echo "Installed WordPress ${WP_VERSION} test suite in ${WP_TESTS_DIR}"
