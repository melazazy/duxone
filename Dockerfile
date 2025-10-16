FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN docker-php-ext-install intl

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Node & npm (optional if you want to build assets here instead of node container)
# RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
#     && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
