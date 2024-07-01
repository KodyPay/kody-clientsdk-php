FROM php:8.3-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY public/ /app/public
COPY composer.json /app

RUN apk add --update linux-headers \
   && apk add --no-cache autoconf gcc make g++ zlib-dev git cmake \
   && pecl channel-update pecl.php.net \
   # Install the grpc extension
   && pecl install grpc \
   && docker-php-ext-enable grpc

# INstall gRPC PHP plugin
RUN cd /app && git clone https://github.com/grpc/grpc \
   && cd /app/grpc && git submodule update --init \
   && mkdir -p cmake/build && cd cmake/build && cmake ../.. && make protoc grpc_php_plugin

RUN apk add protobuf

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --optimize-autoloader --no-interaction --no-progress 

WORKDIR /app/public

CMD ["php-fpm"]
