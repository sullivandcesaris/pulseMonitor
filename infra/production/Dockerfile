FROM composer:2 AS dependencies

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-req=ext-pcntl

FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js tsconfig.json components.json postcss.config.js tailwind.config.js ./
COPY --from=dependencies /app/vendor/tightenco/ziggy ./vendor/tightenco/ziggy
RUN npm run build

FROM serversideup/php:8.4-fpm-nginx AS application

WORKDIR /var/www/html

ENV AUTORUN_ENABLED=true \
    PHP_OPCACHE_ENABLE=1 \
    APP_ENV=production \
    APP_DEBUG=false

COPY --chown=www-data:www-data . .
COPY --from=dependencies --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

HEALTHCHECK --interval=30s --timeout=5s --retries=3 CMD php artisan about --only=environment >/dev/null || exit 1
