# Use PHP 8.3 FPM image as the base
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Avoid running as root
RUN useradd -G www-data,root -u 1000 -d /home/user user
RUN mkdir -p /home/user/.composer && \
    chown -R user:user /home/user

# Switch to your new user in the docker image
USER user

# Copy the application code
COPY --chown=user:user . /var/www

# Copy .env.example to .env
RUN cp .env.example .env

# Install project dependencies
RUN composer install
# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]


