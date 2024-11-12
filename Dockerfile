FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libldap2-dev \
    zip \
    unzip \
    openssl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/
RUN docker-php-ext-install ldap
RUN docker-php-ext-install ctype iconv

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create JWT directory
RUN mkdir -p config/jwt

# Copy existing application directory
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"] 