#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    if [ -f .env.docker ]; then
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
    echo "[entrypoint] Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --ignore-platform-reqs
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force --no-interaction
fi

php artisan migrate --force --no-interaction

if [ ! -f storage/.docker-initialized ]; then
    echo "[entrypoint] Running initial database seed..."
    php artisan db:seed --force --no-interaction
    touch storage/.docker-initialized
fi

php artisan storage:link --force --no-interaction 2>/dev/null || true

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "[entrypoint] Application ready"
exec "$@"
