FROM php:8.3-cli

WORKDIR /var/www

# System deps + postgres driver
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Cache (safe even if some fail)
RUN php artisan config:cache || true \
 && php artisan route:cache || true \
 && php artisan view:cache || true

# Render uses PORT env var (default 10000)
CMD sh -c "php artisan migrate --force && php -S 0.0.0.0:${PORT:-10000} -t public"
