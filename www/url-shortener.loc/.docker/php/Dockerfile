FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        mc \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Установка mcrypt
RUN apt-get update && apt-get install -y libmcrypt-dev \
    && pecl install mcrypt \
    && docker-php-ext-enable mcrypt

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/conf.d/40-custom.ini

WORKDIR /var/www

CMD ["php-fpm"]

HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1
