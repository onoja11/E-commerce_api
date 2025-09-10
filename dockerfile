# Use official PHP 8.2 FPM image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip \
    curl \
    git \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy all project files to container
COPY . .

# Create persistent SQLite database file in /var (Render allows persistence here)
RUN mkdir -p /var && touch /var/database.sqlite

# Install PHP dependencies (for production)
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets only if a package.json exists
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Set correct permissions for Laravel
RUN chmod -R 755 storage bootstrap/cache database

# Expose Laravel's default serve port
EXPOSE 8000

# Run migrations, optimize config, and start Laravel server
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan storage:link && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=8000



