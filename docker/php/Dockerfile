# Stage 1: Build Vite assets
FROM node:20 AS vite-builder

WORKDIR /app

# Only copy frontend-related files for caching purposes
COPY package.json package-lock.json vite.config.js ./
COPY resources resources

# Install dependencies and build
RUN npm install && npm run build


# Stage 2: Laravel app with PHP and Composer
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

# Trust this folder for git (if applicable)
RUN git config --global --add safe.directory /var/www

# Copy composer files and install dependencies first (cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --no-scripts

# Copy the rest of the Laravel app
COPY . .

# Finalize Composer
RUN composer dump-autoload --optimize

# Copy Vite build from Node stage
COPY --from=vite-builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

CMD ["php-fpm"]
