FROM php:8.2-fpm-alpine

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql \
    && apk del .build-deps

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/backend

COPY backend/ /var/www/backend/

RUN mkdir -p /var/www/backend/public/uploads \
    && chown -R www-data:www-data /var/www/backend/public/uploads
