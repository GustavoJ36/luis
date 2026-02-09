FROM php:8.4-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    p7zip-full \
    git \
    nano

RUN docker-php-ext-install gd

RUN apt-get install -y \
    build-essential \
    curl

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install pcntl;

RUN apt-get update && apt-get install -y libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql

RUN apt-get install -y wget

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs

# Install Laravel installer globally
RUN composer global require laravel/installer
ENV PATH="$PATH:/root/.composer/vendor/bin"


