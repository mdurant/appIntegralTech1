# -----------------------------------------------------------------------------
# App IntegralTech - Docker multi-stage para GCP Cloud Run
# PHP 8.4, Nginx, Laravel 12, Vite build
# -----------------------------------------------------------------------------
# Orden: 1) Composer (genera vendor), 2) Frontend (necesita vendor para flux.css), 3) Runtime
# -----------------------------------------------------------------------------

# ---- Stage 1: Composer dependencies ----
FROM composer:2 AS composer

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_NO_INTERACTION=1

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

# ---- Stage 2: Frontend (Vite / Tailwind) ----
# Necesita vendor (flux.css, @source) desde composer
FROM node:22-alpine AS frontend

WORKDIR /app

COPY --from=composer /app/vendor ./vendor
COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# ---- Stage 3: Runtime (Nginx + PHP-FPM) ----
FROM php:8.4-fpm-alpine AS runtime

# PHP extensions required by Laravel (+ SQLite, GD for DomPDF, etc.)
# pkgconf + *-dev needed so PHP extension configure (gd, zip, intl) find libraries via pkg-config
RUN apk add --no-cache \
    pkgconf \
    nginx \
    wget \
    sqlite-libs \
    sqlite-dev \
    libzip \
    libzip-dev \
    libpng \
    libpng-dev \
    freetype \
    freetype-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    oniguruma \
    oniguruma-dev \
    icu \
    icu-dev \
    zlib \
    zlib-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        pdo \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        zip \
        gd \
        intl \
        opcache

# OPcache for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini

# Nginx runs on PORT (Cloud Run)
ENV PORT=8080
ENV NGINX_PORT=8080

# Non-root user (best practice)
RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

COPY --from=composer /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf.template
COPY docker/entrypoint.sh /entrypoint.sh

RUN rm -f /etc/nginx/http.d/default.conf 2>/dev/null || true \
    && chown -R app:app /var/www/html \
    && chmod +x /entrypoint.sh \
    && chown -R app:app /var/lib/nginx /var/log/nginx \
    && mkdir -p /var/www/html/storage/framework/cache/data \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/logs \
        /var/www/html/bootstrap/cache \
    && chown -R app:app /var/www/html/storage /var/www/html/bootstrap/cache

# Entrypoint runs as root to write nginx config; nginx workers run as app (see nginx.conf)

EXPOSE 8080

# Optional: Cloud Run can use HTTP health checks; container listens on PORT (default 8080)
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=2 \
    CMD wget -q -O- http://127.0.0.1:8080/ || exit 1

ENTRYPOINT ["/entrypoint.sh"]
