FROM php:8.3.12-fpm

RUN apt-get update  \
    && apt-get install -y libzip-dev unzip git \
    && docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
