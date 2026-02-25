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

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


# Eigene PHP-Konfiguration für Timeouts und Limits
COPY custom.ini /usr/local/etc/php/conf.d/custom.ini

# Stelle sicher, dass der Container mit dem gewünschten User läuft
ARG UID=1000
ARG GID=1000
RUN addgroup --gid $GID appgroup && \
    adduser --disabled-password --gecos '' --uid $UID --gid $GID appuser
USER appuser

ENTRYPOINT ["entrypoint.sh"]
