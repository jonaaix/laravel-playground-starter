#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

random_password() {
    openssl rand -base64 32 | tr '+/=' '___'
}

set_env() {
    local key="$1" value="$2" tmp
    tmp="$(mktemp)"
    ENV_KEY="$key" ENV_VALUE="$value" awk '
        BEGIN { key = ENVIRON["ENV_KEY"]; value = ENVIRON["ENV_VALUE"]; found = 0 }
        $0 ~ "^" key "=" { print key "=" value; found = 1; next }
        { print }
        END { if (!found) print key "=" value }
    ' .env > "$tmp"
    mv "$tmp" .env
}

if [[ -e .env ]]; then
    echo "A .env file already exists. Remove it first to run the quick setup again." >&2
    exit 1
fi

read -r -p "Domain (leave empty for a local setup): " domain
domain="${domain#http://}"
domain="${domain#https://}"
domain="${domain%/}"

if [[ -n "$domain" && ! "$domain" =~ ^[A-Za-z0-9.-]+\.[A-Za-z]{2,}$ ]]; then
    echo "\"$domain\" is not a valid domain." >&2
    exit 1
fi

orbit_release_key=""
while [[ -z "$orbit_release_key" || "$orbit_release_key" == *"'"* ]]; do
    read -r -s -p "Orbit release key: " orbit_release_key
    echo
done

cp .env.example .env

set_env APP_KEY "base64:$(openssl rand -base64 32)"
set_env MEDIA_HASH_SALT "$(openssl rand -hex 32)"
set_env REDIS_PASSWORD "$(random_password)"
set_env MEILISEARCH_KEY "$(random_password)"
set_env REVERB_APP_KEY "$(random_password)"
set_env REVERB_APP_SECRET "$(random_password)"
set_env ORBIT_RELEASE_KEY "'$orbit_release_key'"

if [[ -n "$domain" ]]; then
    set_env LARAVEL_DOMAIN "$domain"
    set_env PMA_DOMAIN "pma.$domain"
    set_env APP_URL "https://$domain"
    set_env VITE_HMR_HOST "vite.$domain"
    set_env VITE_HMR_PROTOCOL wss
    set_env VITE_HMR_CLIENT_PORT 443
    set_env REVERB_HOST "\"ws.$domain\""
    set_env REVERB_PORT 443
    set_env REVERB_SCHEME https
    ln -sfn compose.prod.yaml compose.yaml
else
    ln -sfn compose.local.yaml compose.yaml
fi

echo
echo ".env created for ${domain:-a local setup}. Next steps:"
echo "  docker compose up -d"
echo "  docker compose exec php php artisan migrate --force"
echo "  docker compose exec php php artisan db:seed --force"
echo "  docker compose exec php php artisan webpush:vapid"
