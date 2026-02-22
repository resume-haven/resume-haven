FROM php:8.5-fpm

# System packages
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        intl \
        gd \
        bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer installieren
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Erstelle notwendige Verzeichnisse mit korrekten Permissions
RUN mkdir -p storage/logs bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html
