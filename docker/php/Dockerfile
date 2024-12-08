FROM php:8.2-apache-buster

# PUBLIC DIRECTORY
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    vim \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-client \
    libzip-dev

# Clear cache
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl zip

COPY xdebug.ini "$PHP_INI_DIR/xdebug.ini"
ARG INSTALL_XDEBUG=false
RUN if [ ${INSTALL_XDEBUG} = true ]; then \
  pecl install xdebug \
  && sed -e "/^zend_extension/d" \
  && docker-php-ext-enable xdebug \
  && cp "${PHP_INI_DIR}/xdebug.ini" "${PHP_INI_DIR}/conf.d/xdebug.ini" \
;fi

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
#COPY .env.dev /var/www/html/.env

#RUN chown -R www-data:www-data /var/www/html/

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
