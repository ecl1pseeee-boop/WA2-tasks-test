#!/bin/sh
set -e

# Права на storage/cache (bind-mount с хоста, php-fpm под www-data).
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# vendor/ приходит из bind-mount и в образе его нет — ставим здесь, при старте,
# один раз (на чистом клоне), дальше пропускаем, чтобы не тормозить рестарт.
if [ ! -f vendor/autoload.php ]; then
  echo "Installing composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Грубое ожидание MySQL — для учебной заготовки сойдёт.
echo "Waiting for MySQL..."
until php -r "try { new PDO('mysql:host=mysql;dbname=boardy','boardy','boardy'); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
  sleep 2
done
echo "MySQL is up."

php artisan key:generate --force 2>/dev/null || true

# Миграции + сид при старте (сидер идемпотентный — не задваивает).
php artisan migrate --force || true
php artisan db:seed --force || true

exec "$@"
