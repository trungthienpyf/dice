FROM php:8.2-fpm-alpine
ARG NGINX_ENV=nginx.conf
# Set working directory
WORKDIR /opt/laravel

# Install additional packages
RUN apk --no-cache add \
    nginx \
    supervisor \
    libmcrypt-dev \
     mariadb-client \
     mariadb-dev \
     && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable opcache

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
&& pecl install redis \
&& docker-php-ext-enable redis
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy Nginx configuration
COPY conf.d/nginx/${NGINX_ENV} /etc/nginx/nginx.conf

# Copy PHP configuration
COPY conf.d/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

COPY conf.d/php/php.ini /usr/local/etc/php/conf.d/php.ini

COPY conf.d/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy Supervisor configuration
COPY conf.d/supervisor/supervisord.conf /etc/supervisord.conf

# Copy Laravel application files
COPY . /opt/laravel

# Set up permissions
RUN chown -R www-data:www-data /opt/laravel \
    && chmod -R 755 /opt/laravel/storage

# Scheduler setup


# Expose ports
EXPOSE 80

ADD conf.d/entrypoint.sh /root/entrypoint.sh
RUN chmod -c 755 /root/entrypoint.sh
ENTRYPOINT ["sh","/root/entrypoint.sh"]
