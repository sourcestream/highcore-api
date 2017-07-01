#!/bin/bash
set -e

rm -rf /app/storage/logs/laravel.log
[[ "$DB_MIGRATE" == "true" ]] && dockerize -stdout /app/storage/logs/laravel.log -wait tcp://db:5432 -timeout 20s php /app/artisan migrate --seed --force

chown -R www-data:www-data /app/storage
docker-php-entrypoint dockerize -stdout /app/storage/logs/laravel.log -wait tcp://db:5432 -timeout 20s php-fpm