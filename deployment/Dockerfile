FROM nlcdigital/base-php-72-image 
RUN mkdir -p /var/www/web/
RUN mkdir -p /var/www/vendor/
RUN mkdir -p /var/www/config/
RUN mkdir -p /var/www/scripts/
WORKDIR /var/www/
COPY web/ /var/www/web/
COPY vendor/ /var/www/vendor/
COPY config/ /var/www/config/
COPY scripts/ /var/www/scripts/
# do we need this load env ?
COPY load.environment.php /var/www/
COPY composer.json /var/www/
COPY phpunit.xml.dist /var/www/
COPY build-version.txt /var/www/
COPY deployment/.vaultenv.secrets-prod /var/www/.vaultenv.secrets-prod
COPY deployment/.vaultenv.secrets-staging /var/www/.vaultenv.secrets-staging
COPY deployment/vaultenv-wrapper /usr/bin/vaultenv-wrapper
RUN chmod +x /usr/bin/vaultenv-wrapper 
# this command sets the env vars in the relevant .vaultenv.secrets file for the php process
CMD ["/usr/bin/vaultenv-wrapper", "php-fpm"]

