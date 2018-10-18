FROM php:5.4-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    wget \
    libmcrypt-dev \
 && rm -rf /var/lib/apt/lists/*

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

RUN docker-php-ext-install pdo_pgsql mcrypt

COPY migrate-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["migrate-entrypoint.sh"]
#
#COPY ./ /app

#RUN chown -R www-data:www-data /app/app/cache \
#&& chown -R www-data:www-data /app/app/logs \
