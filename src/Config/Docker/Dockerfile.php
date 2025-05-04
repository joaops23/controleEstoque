FROM php:8.2-fpm

# Instalar dependências (se necessário)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

COPY composer.json composer.lock ./

WORKDIR /usr/share/nginx/html
