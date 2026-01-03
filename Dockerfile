# 1. Utilisation de PHP 8.4 (requis par ton composer.json)
FROM php:8.4-fpm

# 2. Installation des dépendances système
RUN apt-get update && apt-get install -y \
    nginx \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_pgsql zip \
    && pecl install apcu && docker-php-ext-enable apcu

# 3. Configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 4. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. On copie d'abord uniquement les fichiers composer pour optimiser le cache Docker
COPY composer.json composer.lock* ./

# 6. Installation des dépendances (SANS scripts pour éviter l'erreur de DB au build)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 7. Copie du reste du projet
COPY . .

# 8. Création des dossiers et compilation
# On définit une DATABASE_URL bidon pour empêcher Symfony de râler pendant le build
# On vide aussi le cache avant pour être sûr de partir sur du propre
RUN mkdir -p var/cache var/log var/sessions
RUN chown -R www-data:www-data var/ || true
RUN chmod -R 777 var/ || true
RUN chmod -R 777 var/ || true

ENV APP_ENV=prod
ENV DATABASE_URL=postgresql://null:null@127.0.0.1:5432/null

# 1. Télécharger les assets JS externes (Stimulus, Turbo, etc.)
RUN php bin/console importmap:install

# 2. Build de Tailwind (génère le CSS)
RUN php bin/console tailwind:build --minify

# 3. Compilation finale
RUN php bin/console asset-map:compile

RUN php bin/console tailwind:build --minify
RUN php bin/console asset-map:compile

# 9. Script de démarrage (Migrations + Nginx + FPM)
# ... tes étapes précédentes (compilation assets, etc.) ...

EXPOSE 80

# On lance les migrations ET les services directement ici
# Le "&&" fait que si les migrations échouent, le serveur ne démarre pas (utile pour voir l'erreur dans les logs)
CMD php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration && php-fpm -D && nginx -g "daemon off;"