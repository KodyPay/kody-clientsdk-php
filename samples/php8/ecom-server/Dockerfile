FROM php:8.3-fpm-bullseye

RUN apt-get -qq update && apt-get -qq install -y \
   git \
   zip \
   unzip \
   libprotobuf-dev \
   protobuf-compiler

RUN pecl install grpc && \
   docker-php-ext-enable grpc

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY public/ public/
COPY composer.json ./

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --optimize-autoloader --no-interaction

WORKDIR /app/public

CMD ["php-fpm"]
