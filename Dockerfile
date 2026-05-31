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
# Permitir que Apache interprete .htaccess en public (rutas amigables Laravel)
RUN echo '<Directory /var/www/html/public>\n    AllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# Copy Laravel project files
COPY . .

# Copy the production environment file before any build steps
RUN if [ -f .env.render ]; then cp .env.render .env; fi

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
# Copia el script de espera y dale permisos de ejecución
COPY wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh

# Copia el archivo de entorno de producción si existe
RUN if [ -f .env.render ]; then cp .env.render .env; fi
# Usa el script antes de migrar y arrancar Apache
CMD /usr/local/bin/wait-for-db.sh && php artisan config:clear && php artisan migrate:fresh --seed --force && apache2-foreground