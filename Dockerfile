# Use an official PHP runtime as the base image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
git zlib1g-dev libpng-dev libzip-dev \
zip unzip autoconf g++ make vim wget

# NodeJS
RUN set -uex; \
apt-get update; \
apt-get install -y ca-certificates curl gnupg; \
mkdir -p /etc/apt/keyrings; \
curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
| gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
NODE_MAJOR=20; \
echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" \
> /etc/apt/sources.list.d/nodesource.list; \
apt-get -qy update; \
apt-get -qy install nodejs;

# Install PHP extensions
RUN docker-php-ext-install zip pcntl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY app app
COPY config config
COPY database/factories database/factories
COPY database/migrations database/migrations
COPY database/seeders database/seeders
COPY public public
COPY routes routes
COPY bootstrap bootstrap
COPY package.json .
COPY package-lock.json .
COPY vite.config.js .
COPY tailwind.config.js .
COPY postcss.config.js .
COPY artisan .

RUN ln -s /var/www/html/artisan /bin/artisan

# Install PHP dependencies
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY composer.json .
COPY composer.lock .
RUN composer install --prefer-dist --no-scripts --no-autoloader --no-dev && \
composer dump-autoload --optimize

COPY resources/css resources/css
COPY resources/fonts resources/fonts
COPY resources/views resources/views
COPY resources/js resources/js
RUN npm install && npm run build

COPY storage storage
COPY deployment/env.docker .env

COPY deployment/apache.conf /etc/apache2/sites-available/000-default.conf
COPY deployment/.htaccess /var/www/html/.htaccess

RUN chown -R www-data: /var/www
RUN chmod -R u+w /var/www

# Enable Apache modules
RUN a2enmod remoteip
RUN a2enmod rewrite
RUN a2enmod deflate

RUN apachectl -t
RUN apachectl -v

# Expose the port on which Apache will listen
EXPOSE 80

CMD ["apache2-foreground"]
