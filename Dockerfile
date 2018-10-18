FROM php:5-fpm

RUN apt-get update && apt-get install -y \
    openssh-client \
    git \
    libpq-dev \
    wget \
    libmcrypt-dev \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
 && curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add - \
 && add-apt-repository \
    "deb [arch=amd64] https://download.docker.com/linux/debian \
    $(lsb_release -cs) \
    stable" \
 && apt-get update && apt-get install -y \
    docker-ce \
 && rm -rf /var/lib/apt/lists/*

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

RUN docker-php-ext-install pdo_pgsql mcrypt
RUN chown www-data /var/www/
RUN usermod -aG docker www-data
RUN curl -L "https://github.com/docker/compose/releases/download/1.22.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose \
    && chmod a+x /usr/local/bin/docker-compose

ENV DIND_COMMIT 52379fa76dee07ca038624d639d9e14f4fb719ff
RUN curl -sSL https://raw.githubusercontent.com/docker/docker/${DIND_COMMIT}/hack/dind -o /usr/local/bin/dind \
    && chmod a+x /usr/local/bin/dind

COPY .docker/api/php.ini /usr/local/etc/php/conf.d/php.ini
COPY .docker/api/migrate-entrypoint.sh /usr/local/bin/
COPY . /app

ENTRYPOINT ["migrate-entrypoint.sh"]
VOLUME /var/lib/docker
#
#COPY ./ /app

#RUN chown -R www-data:www-data /app/app/cache \
#&& chown -R www-data:www-data /app/app/logs \
