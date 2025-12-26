FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libgmp-dev \
    zlib1g-dev \
    unzip \
    && docker-php-ext-install \
    pdo_pgsql \
    gmp \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_pgsql zip gmp

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html