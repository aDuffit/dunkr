FROM php:8.2-fpm

# 1. Installation des dépendances système + extensions PHP
# Ajout de libpng, libjpeg, libwebp pour éviter les erreurs liées aux assets/images
RUN apt-get update && apt-get install -y \
    nginx \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install intl pdo pdo_pgsql zip gd

# 2. Configuration Nginx (on s'assure que le dossier existe)
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 3. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Préparation du projet
WORKDIR /var/www/html
COPY . .

# 5. Nettoyage et Installation Composer
# On supprime le dossier vendor et le lock s'ils existent pour repartir sur du propre
RUN rm -rf vendor composer.lock
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 6. Droits et Compilation (AssetMapper remplace Webpack Encore ici)
RUN chown -R www-data:www-data var/
RUN php bin/console asset-map:compile

# 7. Script de démarrage robuste (Migrations + Start)
RUN echo "#!/bin/sh\n\
# On attend que la DB soit prête si possible, puis migration\n\
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration\n\
service php8.2-fpm start\n\
nginx -g 'daemon off;'" > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]