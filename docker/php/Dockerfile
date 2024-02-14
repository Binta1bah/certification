# Utilise l'image de base php:8.2-apache
FROM php:8.2-apache
WORKDIR /var/www/html
COPY . .

# Ajoute la directive "ServerName localhost" au fichier de configuration d'Apache pour éviter les avertissements
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Met à jour la liste des paquets disponibles et installe les paquets nécessaires via APT
RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
     vim \
     locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

# Configure et génère les locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Télécharge et installe Composer, un gestionnaire de dépendances PHP
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Configure l'extension PHP intl pour l'installation ultérieure
RUN docker-php-ext-configure intl

# Installe plusieurs extensions PHP et active le module Apache rewrite
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite

# Installe l'extension PHP APCu via PECL et l'active pour une utilisation avec PHP
RUN pecl install apcu && docker-php-ext-enable apcu

# Ajoute un script pour l'installation des extensions PHP à partir d'une URL externe    
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Donne les permissions d'exécution au script et installe l'extension PHP amqp
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp
