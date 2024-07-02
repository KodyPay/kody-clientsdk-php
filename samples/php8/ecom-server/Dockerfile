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

RUN cmake ../.. && \
  make protoc grpc_php_plugin

RUN pecl install grpc

######################
FROM php:8.3-fpm-bullseye

RUN apt-get -qq update && apt-get -qq install -y git

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY --from=grpc-base /github/grpc/cmake/build/third_party/protobuf/protoc \
  /usr/local/bin/protoc

COPY --from=grpc-base /github/grpc/cmake/build/grpc_php_plugin \
  /usr/local/bin/protoc-gen-grpc

COPY --from=grpc-base \
  /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so \
  /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so

RUN docker-php-ext-enable grpc

COPY public/ /app/public
COPY composer.json /app

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN apt-get install -y protobuf-compiler zip unzip
RUN composer install --optimize-autoloader --no-interaction --no-progress 

WORKDIR /app/public

CMD ["php-fpm"]
