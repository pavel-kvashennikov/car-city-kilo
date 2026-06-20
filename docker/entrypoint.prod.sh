#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    if [ -f .env.prod ]; then
        cp .env.prod .env
    elif [ -f .env.docker ]; then
        cp .env.docker .env
    else
        cp .env.example .env
    fi
    echo "[entrypoint] Created .env"
fi

echo "[entrypoint] Waiting for PostgreSQL..."
until php -r 'try { new PDO("pgsql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT").";dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); exit(0); } catch (Throwable $e) { exit(1); }' 2>/dev/null; do
    sleep 2
done
echo "[entrypoint] PostgreSQL is ready"

if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] Installing Composer dependencies (production)..."
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force --no-interaction
fi

if [ ! -f public/build/manifest.json ]; then
    echo "[entrypoint] ERROR: Frontend assets not built. Run:"
    echo "  docker compose -f docker-compose.prod.yml --profile build run --rm frontend"
    exit 1
fi

php artisan migrate --force --no-interaction

if [ "${RUN_DB_SEED:-false}" = "true" ] && [ ! -f storage/.docker-initialized ]; then
    echo "[entrypoint] Running initial database seed..."
    php artisan db:seed --force --no-interaction
    touch storage/.docker-initialized
fi

php artisan storage:link --force --no-interaction 2>/dev/null || true

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

if [ "${APP_ENV:-production}" = "production" ]; then
    echo "[entrypoint] Warming up Laravel caches..."
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
    php artisan event:cache --no-interaction
fi

echo "[entrypoint] Application ready"
exec "$@"
