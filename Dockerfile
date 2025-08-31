# Gunakan PHP + Apache
FROM php:8.2-apache

# Install extension yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy semua file Laravel ke container
COPY . /var/www/html

# Set working dir
WORKDIR /var/www/html

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Set Laravel public sebagai document root Apache
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Permissions untuk Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
