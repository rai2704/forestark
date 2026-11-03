#!/usr/bin/env bash
# Atualiza a produção a partir do Git. Rode como root, na raiz do projeto:
#   bash deploy.sh
# A credencial do GitHub fica com o root; o resto roda como o dono do site.
set -euo pipefail

cd "$(dirname "$0")"
OWNER=$(stat -c %U artisan)
as_owner() { sudo -u "$OWNER" -H bash -c "cd '$PWD' && $*"; }

as_owner "php artisan down --retry=30" || true
trap 'as_owner "php artisan up"' EXIT

git -c safe.directory="$PWD" pull --ff-only
chown -R "$OWNER:$OWNER" .

as_owner "composer install --no-dev --optimize-autoloader --no-interaction -q"
as_owner "npm ci --no-audit --no-fund --silent"
as_owner "npm run build --silent"
as_owner "rm -rf node_modules"
as_owner "php artisan migrate --force"
as_owner "php artisan optimize"
