#!/usr/bin/env bash

set -euo pipefail

composer install --no-dev --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build
