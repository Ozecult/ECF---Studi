# Image de base PHP 8.2 avec Apache
FROM php:8.2-apache

# Metadonnées
LABEL maintainer="Sandy PODVIN <sandypodvin@gmail.com>"
LABEL project="EcoRide - Plateforme de covoiturage écologique"
LABEL version="1.0"

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libssl-dev \
    pkg-config \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP requises
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd

# Installation de l'extension MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration d'Apache
RUN a2enmod rewrite headers

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . /var/www/html/

# Installation des dépendances Composer
RUN if [ -f composer.json ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction; \
    fi

# Création du dossier uploads avec permissions
RUN mkdir -p /var/www/html/uploads/photos \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

# Permissions sur le dossier complet
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configuration PHP personnalisée
RUN echo "upload_max_filesize = 2M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 2M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Exposition du port 80
EXPOSE 80

# Démarrage d'Apache en premier plan
CMD ["apache2-foreground"]
