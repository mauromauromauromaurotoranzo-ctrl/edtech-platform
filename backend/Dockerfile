FROM php:8.4-fpm

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

# Create necessary directories
RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data bootstrap storage

# Copy composer files
COPY composer.json ./

# Install dependencies (sin lock file, sin scripts)
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Copy application
COPY . .

# Set permissions before autoload
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chmod -R 755 /var/www/storage

# Generate autoloader
RUN composer dump-autoload --optimize

# Final permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
