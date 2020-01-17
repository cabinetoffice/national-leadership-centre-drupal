FROM php:7.2-fpm
RUN mkdir -p /var/www/web/
RUN mkdir -p /var/www/vendor/
RUN mkdir -p /var/www/config/
RUN mkdir -p /var/www/drush/
RUN mkdir -p /var/www/scripts/
WORKDIR /var/www/
COPY web/ /var/www/web/
COPY vendor/ /var/www/vendor/
COPY config/ /var/www/config/
COPY drush/ /var/www/drush/
COPY scripts/ /var/www/scripts/
COPY load.environment.php /var/www/
COPY composer.json /var/www/
COPY phpunit.xml.dist /var/www/
COPY build-version.txt /var/www/
COPY .vaultenv.secrets /var/www/.vaultenv.secrets
COPY docker-build/etc-profile-d-drush-path.sh /etc/profile.d/drush-path.sh

# php extenstions needed for drupal
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install opcache

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd  \
    && docker-php-ext-install -j$(nproc) gd

# prod php ini - needs logging setup 
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# TODO - set up opcache config 



# the following are for getting Vault secrets into the environment for php

ADD https://github.com/channable/vaultenv/releases/download/v0.11.0/vaultenv-v0.11.0-linux-musl /usr/bin/vaultenv
RUN chmod +x /usr/bin/vaultenv

COPY docker-build/vaultenv-wrapper /usr/bin/vaultenv-wrapper
RUN chmod +x /usr/bin/vaultenv-wrapper 

CMD ["/usr/bin/vaultenv-wrapper", "php-fpm"]

# e.g. CMD /usr/bin/vaultenv  --no-connect-tls --log-level info --token "here" --secrets-file /var/www/.vaultenv.secrets env
