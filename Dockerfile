# =============================================================================
# GovKloud Staging/Production Docker Image
# PHP 8.4 + Nginx + Redis + kubectl + helm + vcluster
# =============================================================================
FROM php:8.4-fpm-bookworm AS base

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    redis-server \
    openssh-server \
    curl \
    unzip \
    git \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        xml \
        gd \
        zip \
        bcmath \
        pcntl \
        opcache \
        curl \
        intl \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# =============================================================================
# Install kubectl
# =============================================================================
RUN curl -fsSL "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" \
    -o /usr/local/bin/kubectl \
    && chmod +x /usr/local/bin/kubectl

# =============================================================================
# Install helm
# =============================================================================
RUN curl -fsSL https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash

# =============================================================================
# Install vcluster CLI
# =============================================================================
RUN curl -fsSL https://github.com/loft-sh/vcluster/releases/latest/download/vcluster-linux-amd64 \
    -o /usr/local/bin/vcluster \
    && chmod +x /usr/local/bin/vcluster

# =============================================================================
# Install Composer
# =============================================================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# =============================================================================
# PHP configuration
# =============================================================================
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-custom.ini /usr/local/etc/php/conf.d/99-custom.ini

# =============================================================================
# Nginx configuration
# =============================================================================
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# =============================================================================
# Supervisor configuration (manages all processes)
# =============================================================================
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# =============================================================================
# SSH configuration (Azure App Service compatible)
# =============================================================================
COPY docker/ssh/sshd_config /etc/ssh/sshd_config
RUN mkdir -p /run/sshd

# =============================================================================
# Redis configuration
# =============================================================================
COPY docker/redis/redis.conf /etc/redis/redis-custom.conf

# =============================================================================
# Application setup
# =============================================================================
WORKDIR /var/www/html

# Copy composer files first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy full application
COPY . .

# Finalize composer
RUN composer dump-autoload --optimize --no-dev \
    && php artisan config:clear

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create log directory for supervisor and nginx PID directory
RUN mkdir -p /var/log/supervisor /run

# Copy and set startup script
COPY docker/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

EXPOSE 8080 2222

CMD ["/usr/local/bin/startup.sh"]
