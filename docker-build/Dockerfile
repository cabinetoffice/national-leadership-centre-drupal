FROM php:7.2-fpm

RUN mkdir -p /var/www/web/
RUN mkdir -p /var/www/vendor/
WORKDIR /var/www/
COPY /web/ /var/www/web/
COPY /vendor/ /var/www/vendor/
#COPY composer.json composer.json
#COPY composer.lock composer.lock
#RUN mkdir -p scripts/composer/
#COPY /scripts/composer/ScriptHandler.php scripts/composer/
COPY /load.environment.php /var/www/

