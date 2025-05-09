FROM php:8.2-fpm

# Instalar dependências (se necessário)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    zip \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

COPY composer.json composer.lock ./

WORKDIR /usr/share/nginx/html
