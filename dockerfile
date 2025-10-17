# Use official PHP image with necessary extensions
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer globally
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy files
COPY . .

# Increase Composer memory limit
ENV COMPOSER_MEMORY_LIMIT=-1

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --optimize-autoloader || cat /root/.composer/logs/* || true

# Generate app key (optional if you have APP_KEY in env)
RUN php artisan key:generate || true

# Expose the port Laravel will run on
EXPOSE 8000

# Start the Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
