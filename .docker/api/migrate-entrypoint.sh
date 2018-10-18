#!/bin/bash
set -e

rm -rf /app/storage/logs/laravel.log
[[ "$DB_MIGRATE" == "true" ]] && dockerize -stdout /app/storage/logs/laravel.log -wait tcp://$DB_HOST:5432 -timeout 20s php /app/artisan migrate --seed --force

cp /var/www/.ssh/id_rsa /app/storage/app/id_rsa
cp /var/www/.ssh/id_rsa.pub /app/storage/app/id_rsa.pub
cp /var/www/.ssh/known_hosts /app/storage/app/known_hosts
chown -R www-data:www-data /var/www/.ssh
chown -R www-data:www-data /app/storage

echo "==> Launching the Docker daemon..."
dind dockerd --storage-driver=overlay2 $DOCKER_EXTRA_OPTS &

while(! docker info > /dev/null 2>&1); do
    echo "==> Waiting for the Docker daemon to come online..."
    sleep 1
done
echo "==> Docker Daemon is up and running!"

docker-php-entrypoint dockerize -wait tcp://$DB_HOST:5432 -timeout 20s php-fpm