FROM composer:latest as composer

FROM php:8.3-bullseye as grpc-base

RUN apt-get -qq update && apt-get -qq install -y \
  libprotobuf-dev protobuf-compiler

RUN pecl install grpc

######################
FROM php:8.3-fpm-bullseye

RUN apt-get -qq update && apt-get -qq install -y \
  git zip unzip

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY --from=grpc-base \
  /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so \
  /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so

RUN docker-php-ext-enable grpc

WORKDIR /app
COPY composer.json /app/
COPY public/ /app/public

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --optimize-autoloader --no-interaction

WORKDIR /app/public

CMD ["php-fpm"]
