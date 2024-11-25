# Frontend builder container

FROM node:lts AS ui_builder

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

FROM php:8.3-apache-bookworm AS server

RUN apt-get update && apt-get upgrade -y
RUN apt install -y libxml2-dev git unzip libicu-dev g++

COPY --from=composer:lts /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install mysqli xml fileinfo intl

COPY --from=src_filter /app .

RUN composer --working-dir=lib/pkp --no-progress -o -n --no-dev update
COPY --from=ui_builder /app/js/build.js ./js/build.js

RUN for file in $(find . -name "auth.json"); do rm $file -f; done

RUN chown -R www-data:www-data *

USER www-data:www-data

ENTRYPOINT ["/var/www/html/entrypoint.sh"]
