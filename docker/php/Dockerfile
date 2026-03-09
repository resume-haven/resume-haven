FROM php:8.5-fpm

# Build arguments
ARG INSTALL_XDEBUG=false

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
    ca-certificates \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        intl \
        gd \
        bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Xdebug Installation (optional)
RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
        apt-get update && apt-get install -y \
            autoconf g++ make && \
        pecl install xdebug && \
        docker-php-ext-enable xdebug && \
        apt-get remove -y autoconf g++ make && \
        apt-get clean && rm -rf /var/lib/apt/lists/*; \
    fi

# gosu installieren (um zu appuser zu wechseln)
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends gosu; \
    rm -rf /var/lib/apt/lists/*; \
    gosu nobody true

# Composer installieren
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


# Eigene PHP-Konfiguration für Timeouts und Limits
COPY custom.ini /usr/local/etc/php/conf.d/custom.ini

# Xdebug-Konfiguration (nur wenn installiert)
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN if [ "$INSTALL_XDEBUG" != "true" ]; then \
        rm /usr/local/etc/php/conf.d/xdebug.ini; \
    fi

# PHP-FPM Konfiguration
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Stelle sicher, dass der appuser und appgroup existieren
ARG UID=1000
ARG GID=1000
RUN addgroup --gid $GID appgroup && \
    adduser --disabled-password --gecos '' --uid $UID --gid $GID appuser

ENTRYPOINT ["entrypoint.sh"]
