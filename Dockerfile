# Use official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions for Symfony + MySQL
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    zip \
    default-mysql-client \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    sudo \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set Apache document root to Symfony public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Copy Composer from official image
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html

# Set permissions for Symfony
RUN mkdir -p var vendor \
    && chown -R www-data:www-data var vendor

# Install PHP dependencies with unlimited memory and skip scripts
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Run Symfony auto-scripts as www-data user
RUN sudo -u www-data composer run-script auto-scripts

# Expose port 80
EXPOSE 80

# Run Apache
CMD ["apache2-foreground"]
