# FrankenPHP mit PHP 8.5 und Alpine
FROM dunglas/frankenphp:php8.5-alpine

# Installiere Build Dependencies und System-Pakete
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    autoconf \
    automake \
    build-base \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    icu-dev

# Installiere Composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

# Konfiguriere und installiere GD mit webp/jpeg/freetype Support
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

# Installiere PHP Extensions via install-php-extensions Helper
RUN install-php-extensions \
    pdo_sqlite \
    zip \
    gd \
    bcmath \
    exif \
    intl \
    pcntl \
    sockets \
    xdebug

# Setze den Workdir
WORKDIR /var/www/html

# Kopiere die komplette Applikation
COPY . .

# Erstelle notwendige Verzeichnisse mit korrekten Permissions
RUN mkdir -p storage/logs bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

# Exposiere Ports
EXPOSE 80 443

# Health Check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

# Caddyfile für FrankenPHP
COPY docker/FrankenPHP/Caddyfile /etc/caddy/Caddyfile

# xdebug Konfiguration
COPY docker/FrankenPHP/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini