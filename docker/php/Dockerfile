FROM php:8.4.24-fpm-alpine AS production

RUN apk add --no-cache fcgi icu-libs libjpeg-turbo libpng libwebp freetype oniguruma libzip \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libjpeg-turbo-dev libpng-dev libwebp-dev freetype-dev oniguruma-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" intl mbstring mysqli gd opcache zip \
    && apk del .build-deps \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php/letsbbs.ini /usr/local/etc/php/conf.d/letsbbs.ini
WORKDIR /var/www/html
COPY --from=composer:2.10.2 /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --classmap-authoritative
COPY app app
COPY public public
COPY writable writable
COPY spark preload.php ./
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads public/uploads \
    && chown -R www-data:www-data writable public/uploads
CMD ["php-fpm", "-F"]

FROM production AS test
COPY tests tests
COPY phpunit.dist.xml ./
RUN composer install --no-interaction --no-progress --prefer-dist
CMD ["php", "vendor/bin/phpunit"]
