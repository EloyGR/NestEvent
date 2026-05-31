# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies and required PHP extensions.
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Laravel needs URL rewriting and must be served from /public in production.
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel project files
COPY . .

# Install PHP dependencies optimized for production
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Install Node.js and build frontend assets for production.
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build \
    && rm -f /var/www/html/public/hot \
    && rm -rf /var/lib/apt/lists/*

# Ensure Laravel writable directories exist and are owned by Apache user.
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && rm -rf /var/www/html/public/storage \
    && ln -s /var/www/html/storage/app/public /var/www/html/public/storage \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Apache port
EXPOSE 80

# Espera a que la base de datos esté disponible, luego ejecuta migraciones y arranca Apache
# Espera a que la base de datos esté disponible, mostrando el error real de conexión, luego ejecuta migraciones y arranca Apache
CMD bash -c 'until php -r "try { new PDO(\"pgsql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}\", \"${DB_USERNAME}\", \"${DB_PASSWORD}\"); echo \"DB is up\\n\"; } catch (Exception $e) { echo \"Waiting for DB... Error: ".$e->getMessage()."\\n"; exit(1); }"; do sleep 3; done; php artisan migrate --force && apache2-foreground'