#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ZIP_FILE="${1:-${ROOT_DIR}/dist/mivama-media-folders.zip}"
WP_VERSION="${WP_VERSION:-7.0.2}"
DB_NAME="${DB_NAME:-wordpress_smoke}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-root}"
DB_HOST="${DB_HOST:-127.0.0.1}"
WP_DIR="$(mktemp -d)"

cleanup() {
    rm -rf "${WP_DIR}"
}
trap cleanup EXIT

if [[ ! -f "${ZIP_FILE}" ]]; then
    echo "Release ZIP not found: ${ZIP_FILE}" >&2
    exit 1
fi

command -v wp >/dev/null 2>&1 || {
    echo "WP-CLI is required for the release smoke test." >&2
    exit 1
}

mysql_args=(--host="${DB_HOST}" --user="${DB_USER}")
if [[ -n "${DB_PASS}" ]]; then
    mysql_args+=(--password="${DB_PASS}")
fi

mysqladmin "${mysql_args[@]}" create "${DB_NAME}" 2>/dev/null || true

wp core download \
    --path="${WP_DIR}" \
    --version="${WP_VERSION}" \
    --force \
    --quiet

wp config create \
    --path="${WP_DIR}" \
    --dbname="${DB_NAME}" \
    --dbuser="${DB_USER}" \
    --dbpass="${DB_PASS}" \
    --dbhost="${DB_HOST}" \
    --skip-check \
    --quiet

wp core install \
    --path="${WP_DIR}" \
    --url="https://media-folders.test" \
    --title="Mivama Media Folders Smoke Test" \
    --admin_user="admin" \
    --admin_password="smoke-test-password" \
    --admin_email="admin@example.org" \
    --skip-email \
    --quiet

wp plugin install "${ZIP_FILE}" \
    --path="${WP_DIR}" \
    --activate \
    --quiet

wp plugin is-active mivama-media-folders --path="${WP_DIR}"

wp eval \
    'if ( ! taxonomy_exists( "mivama_media_folder" ) ) { fwrite( STDERR, "Media folder taxonomy is not registered.\n" ); exit( 1 ); }' \
    --path="${WP_DIR}"

wp eval \
    '$role = get_role( "administrator" ); if ( ! $role || ! $role->has_cap( "manage_media_folders" ) ) { fwrite( STDERR, "Administrator folder capability is missing.\n" ); exit( 1 ); }' \
    --path="${WP_DIR}"

echo "Release ZIP installed and activated successfully on WordPress ${WP_VERSION}."
