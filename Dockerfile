FROM php:8.4-fpm-alpine

# Dependencias del sistema y extensiones PHP
RUN apk add --no-cache \
        bash \
        git \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        opcache \
    && apk del $PHPIZE_DEPS

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar dependencias primero (cache de capas)
COPY app/composer.json app/composer.lock ./
RUN composer config --no-plugins policy.advisories.block false \
    && composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction || true

# Copiar el resto de la aplicación
COPY app/ ./

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
