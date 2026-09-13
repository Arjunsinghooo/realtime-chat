FROM node:22-alpine AS frontend

WORKDIR /var/www

COPY package*.json ./

RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build

FROM php:8.4-fpm

WORKDIR /var/www

RUN apt-get update \
    && apt-get install -y unzip libzip-dev \
    && docker-php-ext-install pdo_mysql zip pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

COPY --from=frontend /var/www/public/build ./public/build

RUN composer install --no-interaction --prefer-dist

RUN chown -R www-data:www-data /var/www
