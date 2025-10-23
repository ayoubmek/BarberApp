# Use official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and MySQL client/dev headers
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    zip \
    default-mysql-client \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Composer from official image
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

# Expose port 80
EXPOSE 80

# Run Apache
CMD ["apache2-foreground"]
