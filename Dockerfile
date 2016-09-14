FROM php:5.6-cli

# Install required packages
RUN apt-get update && apt-get install -y libssl-dev libicu-dev git

# Install mongo client libraries
RUN pecl install mongo && docker-php-ext-enable mongo

# enable standard extensions
RUN docker-php-ext-configure intl \
	&& docker-php-ext-install bcmath mbstring intl zip

COPY config/php.ini /usr/local/etc/php/

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

VOLUME /opt/slack-api-export/var

COPY ./src /opt/slack-api-export
WORKDIR /opt/slack-api-export

# install composer
RUN composer install --no-interaction --no-ansi

# setup entrypoint
ADD config/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["echo", "hello from base"]
