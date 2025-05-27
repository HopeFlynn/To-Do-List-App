# Use official PHP image with Apache
FROM php:8.1-apache

# Copy all project files into the web server root
COPY . /var/www/html/

# Enable Apache rewrite module (optional, needed for clean URLs)
RUN a2enmod rewrite

# Expose port 80 (the default for HTTP)
EXPOSE 80
