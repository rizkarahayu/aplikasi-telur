FROM php:8.1-fpm-alpine

WORKDIR /var/www/html

# Install dependencies
RUN apk add --no-cache \
    openssl \
    zip \
    unzip \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy code
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
