FROM php:8.2-fpm

# 1. Installation des dépendances système (libs nécessaires pour les extensions PHP)
RUN apt-get update && apt-get install -y \
    nginx \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_pgsql zip

# 2. Configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 3. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copie du projet
WORKDIR /var/www/html
COPY . .

# 5. Installation des dépendances (SANS les scripts pour éviter les erreurs de DB au build)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 6. Droits et compilation des assets
RUN chown -R www-data:www-data var/
RUN php bin/console asset-map:compile

# 7. Script de démarrage (Migrations + Nginx + FPM)
# On crée un script à la volée pour lancer les migrations AVANT le serveur
RUN echo "#!/bin/sh\n\
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration\n\
service php8.2-fpm start\n\
nginx -g 'daemon off;'" > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]