FROM php:8.2-fpm

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring zip intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /var/www

# Salin source code dan langsung set ownership
COPY --chown=www-data:www-data . .

RUN composer install

# COPY /var/www/.env.example /var/www/.env
# Generate application key
# RUN php artisan key:generate

# Hanya folder yang butuh permission write
RUN chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]

