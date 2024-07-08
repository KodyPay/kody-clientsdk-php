FROM composer:latest as composer

FROM php:8.3-bullseye as grpc-base

RUN apt-get -qq update && apt-get -qq install -y \
  autoconf automake cmake curl git libtool \
  pkg-config unzip zlib1g-dev

ARG MAKEFLAGS=-j8

WORKDIR /github/grpc

RUN git clone https://github.com/grpc/grpc . && \
  git submodule update --init --recursive

WORKDIR /github/grpc/cmake/build

RUN cmake ../.. && make grpc_php_plugin

RUN pecl install grpc

######################
FROM php:8.3-fpm-bullseye

RUN apt-get -qq update && apt-get -qq install -y git

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY --from=grpc-base \
  /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so \
  /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so

RUN docker-php-ext-enable grpc
RUN apt-get install -y zip unzip

COPY public/ /app/public
COPY composer.json /app
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --optimize-autoloader --no-interaction

WORKDIR /app/public

CMD ["php-fpm"]
