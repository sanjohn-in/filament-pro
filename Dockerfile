FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ✅ CORRECT COPY
COPY . .

# Install dependencies (production only)
RUN composer install --no-dev --optimize-autoloader

# Optimize Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Correct permissions
RUN chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]