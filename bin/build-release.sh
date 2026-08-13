#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_SLUG="mivama-media-folders"
DIST_DIR="${ROOT_DIR}/dist"
STAGE_DIR="${DIST_DIR}/${PLUGIN_SLUG}"
ZIP_FILE="${DIST_DIR}/${PLUGIN_SLUG}.zip"

rm -rf "${DIST_DIR}"
mkdir -p "${STAGE_DIR}"

cd "${ROOT_DIR}"

while IFS= read -r -d '' file; do
    rel="${file#./}"
    skip=0
    while IFS= read -r pattern; do
        [[ -z "${pattern}" ]] && continue
        if [[ "${rel}" == "${pattern}" || "${rel}" == "${pattern}/"* ]]; then
            skip=1
            break
        fi
    done < .distignore

    [[ ${skip} -eq 1 ]] && continue
    mkdir -p "${STAGE_DIR}/$(dirname "${rel}")"
    cp -p "${file}" "${STAGE_DIR}/${rel}"
done < <(find . -type f -print0)

php bin/check-version.php

test -f "${STAGE_DIR}/mivama-media-folders.php"
test -f "${STAGE_DIR}/includes/class-mivama-media-folders.php"
test -f "${STAGE_DIR}/readme.txt"
test -f "${STAGE_DIR}/LICENSE"

if [[ -e "${STAGE_DIR}/tests" || -e "${STAGE_DIR}/.github" || -e "${STAGE_DIR}/composer.json" ]]; then
    echo "Release contains development files." >&2
    exit 1
fi

cd "${DIST_DIR}"
zip -qr "${ZIP_FILE}" "${PLUGIN_SLUG}"

echo "Built ${ZIP_FILE}"
