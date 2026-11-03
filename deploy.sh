#!/usr/bin/env bash
# Atualiza a produção a partir do Git. Rode na raiz do projeto, como o usuário do site.
set -euo pipefail

php artisan down --retry=30 || true
trap 'php artisan up' EXIT

git pull --ff-only
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build
rm -rf node_modules
php artisan migrate --force
php artisan optimize
