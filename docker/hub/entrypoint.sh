#!/bin/sh
set -eu

mkdir -p /data
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
touch /data/database.sqlite
chown -R www-data:www-data storage bootstrap/cache /data

if [ ! -f /data/app-key ]; then
    php artisan key:generate --show > /data/app-key
fi

export APP_NAME="${APP_NAME:-Realtime Chat}"
export APP_ENV="${APP_ENV:-production}"
export APP_DEBUG="${APP_DEBUG:-false}"
export APP_URL="${APP_URL:-http://localhost:8080}"
export APP_KEY="${APP_KEY:-$(cat /data/app-key)}"
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_DATABASE="${DB_DATABASE:-/data/database.sqlite}"
export SESSION_DRIVER="${SESSION_DRIVER:-database}"
export CACHE_STORE="${CACHE_STORE:-database}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"
export BROADCAST_CONNECTION="${BROADCAST_CONNECTION:-reverb}"
export REVERB_APP_ID="${REVERB_APP_ID:-realtime-chat}"
export REVERB_APP_KEY="${REVERB_APP_KEY:-realtime-chat}"
export REVERB_APP_SECRET="${REVERB_APP_SECRET:-local-realtime-chat-secret}"
export REVERB_HOST="${REVERB_HOST:-127.0.0.1}"
export REVERB_PORT="${REVERB_PORT:-8080}"
export REVERB_SCHEME="${REVERB_SCHEME:-http}"
export REVERB_SERVER_HOST="${REVERB_SERVER_HOST:-0.0.0.0}"
export REVERB_SERVER_PORT="${REVERB_SERVER_PORT:-8080}"

php artisan migrate --force --no-interaction

exec supervisord -c /etc/supervisor/supervisord.conf
