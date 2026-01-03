FROM php:8.2-fpm

# 1. Installation des dépendances et Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    libicu-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_pgsql

# 2. Configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 3. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copie du projet
WORKDIR /var/www/html
COPY . .

# 5. Installation des dépendances Symfony
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# 6. Droits et compilation des assets
RUN chown -R www-data:www-data var/
RUN php bin/console asset-map:compile

# 7. Script de démarrage (lance FPM et Nginx)
EXPOSE 80
CMD service php8.2-fpm start && nginx -g "daemon off;"