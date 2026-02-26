FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files (solo composer.json si no hay lock)
COPY composer.json ./

# Install dependencies (sin lock file)
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Copy application
COPY . .

# Generate autoloader
RUN composer dump-autoload

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
