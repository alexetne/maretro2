# Runtime image for the PHP app (Composer optional)
FROM php:8.3-apache

# Make Composer available without requiring a separate stage
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source
COPY . .

# Enable common extensions and modules, then install deps only if composer.json exists
RUN set -eux; \
    docker-php-ext-install pdo_mysql mysqli; \
    a2enmod rewrite headers; \
    if [ -d public ]; then \
      sed -ri 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/*.conf; \
      sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf; \
    fi; \
    if [ -f composer.json ]; then \
      composer install --no-dev --prefer-dist --no-interaction --no-progress; \
    fi

EXPOSE 3000

CMD ["apache2-foreground"]
