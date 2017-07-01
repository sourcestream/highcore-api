FROM composer:latest

RUN apk --update add \
  libmcrypt-dev \
  autoconf \
  build-base

RUN docker-php-ext-configure mcrypt \
			--with-mcrypt \
		&& docker-php-ext-install  mcrypt