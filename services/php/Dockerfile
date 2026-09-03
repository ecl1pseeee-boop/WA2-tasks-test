FROM php:8.4-fpm

# Бинарник composer нужен в образе: сами исходники (composer.json) приезжают
# позже через bind-mount, поэтому "composer install" не может выполниться
# на этапе сборки — он запускается из docker-entrypoint.sh при старте контейнера.
COPY --from=composer:2.8.4 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
      libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip bcmath \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Код и vendor приходят из bind-mount (см. грех Т6 в docker-compose.yml).
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
