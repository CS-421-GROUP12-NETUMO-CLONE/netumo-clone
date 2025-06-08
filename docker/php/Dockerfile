FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Make Git trust the folder
RUN git config --global --add safe.directory /var/www

# Copy only composer files first (for better layer caching)
COPY composer.json composer.lock ./

# Install dependencies (without autoloader optimization)
RUN composer install --no-dev --no-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Now run the optimized autoloader
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

CMD ["php-fpm"]
