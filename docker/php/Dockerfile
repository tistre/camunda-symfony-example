FROM php:8.2-fpm

# Use the default production configuration
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install intl
RUN apt-get update && apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Install "zip" extension for Composer installs
RUN apt-get update && apt-get install -y libzip-dev \
    && docker-php-ext-install zip

# git and unzip for Composer installs
RUN apt-get update && apt-get install -y \
        git ssh unzip \
    --no-install-recommends && rm -r /var/lib/apt/lists/*

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer