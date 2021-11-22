FROM php:7.4-fpm-alpine
WORKDIR /opt/app
RUN apk add --no-cache --update postgresql-dev zlib-dev libpng-dev libjpeg-turbo-dev && \
    docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install pdo pdo_pgsql gd && \
    curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/bin --filename=composer

COPY docker/config/php/www.conf /usr/local/etc/php-fpm.d
COPY . .
RUN composer install && php bin/console cache:warmup




