# Frontend builder container

FROM node:16-bookworm AS ui_builder

WORKDIR /app

COPY . .

RUN npm install
RUN npm run build

# Filter out ui's source in target image

FROM alpine:3 AS src_filter

WORKDIR /app

COPY . .

RUN rm -rf ./lib/ui-library
RUN rm -rf ./babel.config.js ./package.json ./package-lock.json ./vue.config.js

# Target

FROM php:8.1-apache-bookworm AS server

ARG COMPOSER_TOKEN=""

RUN apt-get update && apt-get upgrade -y
RUN apt install -y libxml2-dev git unzip libicu-dev g++ wget

COPY --from=composer:lts /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql xml fileinfo intl
RUN a2enmod rewrite

COPY --from=src_filter /app .

RUN wget https://getcomposer.org/installer -O composer-setup.php
RUN php composer-setup.php
RUN php composer.phar config --global "github-oauth.github.com" $COMPOSER_TOKEN
RUN php -d default_socket_timeout=720 composer.phar --working-dir=lib/pkp --no-progress -o -n --no-dev install
RUN php -d default_socket_timeout=720 composer.phar --working-dir=plugins/generic/citationStyleLanguage --no-progress -o -n --no-dev install

COPY --from=ui_builder /app/js/build.js ./js/build.js
COPY --from=ui_builder /app/styles/build.css ./styles/build.css

# Cleanup github credentials for composer
RUN for file in $(find . -name "auth.json"); do rm $file -f; done
RUN rm -rf ~/.composer

EXPOSE 80

RUN mkdir cache
RUN chown -R www-data:www-data /var/www/html

RUN rm /etc/apache2/sites-available/default-ssl.conf
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

USER www-data:www-data

ENTRYPOINT ["/var/www/html/entrypoint.sh"]
