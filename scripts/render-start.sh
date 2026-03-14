#!/usr/bin/env bash

set -euo pipefail

STORAGE_PATH="${LARAVEL_STORAGE_PATH:-}"

if [[ -n "${STORAGE_PATH}" ]]; then
    mkdir -p \
        "${STORAGE_PATH}/app/public" \
        "${STORAGE_PATH}/framework/cache" \
        "${STORAGE_PATH}/framework/sessions" \
        "${STORAGE_PATH}/framework/views" \
        "${STORAGE_PATH}/logs"
fi

php artisan migrate --force --no-interaction
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
php artisan queue:restart || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
