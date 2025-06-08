FROM php:8.3-fpm

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy code (optional if volumes override this anyway)
COPY . .

# Install Laravel dependencies during image build
RUN composer install --no-dev --optimize-autoloader
