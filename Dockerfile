# Use official PHP CLI image
FROM php:8.2-cli

WORKDIR /app

# Install dependencies
RUN apt-get update && apt-get install -y \
    unzip git libzip-dev zip && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Symfony project files
COPY . .

# Install Symfony dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 10000
EXPOSE 10000

# Start Symfony server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
