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

# setup entrypoint
ADD config/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["echo", "hello from base"]
