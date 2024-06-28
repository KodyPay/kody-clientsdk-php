FROM php:8.3-fpm
WORKDIR "/application"

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . /application

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        git \
        zip \
        unzip \
        zlib1g-dev \
        libzip-dev \
    && docker-php-ext-install zip \
    && pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer install --optimize-autoloader --no-interaction --no-progress

EXPOSE 9000

CMD ["php-fpm"]
