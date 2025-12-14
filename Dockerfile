# Depod Survey Application - Dockerfile
FROM php:8.4-apache

# Set locale and encoding
ENV LANG=C.UTF-8 \
    LC_ALL=C.UTF-8 \
    APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    locales \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite \
    && echo "C.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Apache configuration with UTF-8 support
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n\
AddDefaultCharset UTF-8' > /etc/apache2/conf-available/depod.conf \
    && a2enconf depod

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
