FROM php:8.3-cli-alpine

# MySQL pour PHP
RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html
COPY index.php .

# Exécution sous un utilisateur non-root
USER www-data

EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]