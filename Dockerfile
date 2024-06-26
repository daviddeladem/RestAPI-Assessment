# Use an official PHP runtime as a parent image
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
        git \
        unzip \
        libpq-dev \
        libicu-dev \
        libzip-dev \
        zip \
        && docker-php-ext-install pdo pdo_pgsql intl zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application code
COPY . .

# Install Symfony CLI (optional but useful for Symfony projects)
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Install dependencies using Composer
RUN composer install --no-scripts --no-autoloader

# Copy environment file
COPY .env .env

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
