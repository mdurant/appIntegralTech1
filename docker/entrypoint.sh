#!/bin/sh
set -e

PORT="${PORT:-8080}"
export NGINX_PORT="$PORT"

# Substitute PORT into nginx config (Cloud Run sets PORT)
sed "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

# Optional: cache config/routes/views in production (requires DB/Redis at startup if used)
if [ "$APP_ENV" = "production" ] && [ -n "$APP_KEY" ]; then
    cd /var/www/html && php artisan config:cache --no-interaction 2>/dev/null || true
    cd /var/www/html && php artisan route:cache --no-interaction 2>/dev/null || true
    cd /var/www/html && php artisan view:cache --no-interaction 2>/dev/null || true
fi

# PHP-FPM in background (listens on 127.0.0.1:9000)
php-fpm -D

# Nginx in foreground (listens on $PORT)
exec nginx -g 'daemon off;'
