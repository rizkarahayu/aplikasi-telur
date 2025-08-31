# Gunakan image resmi PHP + Apache
FROM php:8.2-apache

# Install dependencies
RUN docker-php-ext-install pdo pdo_mysql

# Copy project
COPY . /var/www/html

# Set working dir
WORKDIR /var/www/html

# Set Laravel public sebagai document root
RUN rm -rf /var/www/html/index.html
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Beri permission storage & bootstrap
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
