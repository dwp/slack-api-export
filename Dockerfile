FROM php:5.6-cli

VOLUME /opt/slack-api-export
COPY src /opt/slack-api-export
WORKDIR /opt/slack-api-export

COPY config/php.ini /usr/local/etc/php/

# Install mongo client libraries
RUN apt-get update && \
    apt-get install -y libssl-dev && \
    pecl install mongo && \
    docker-php-ext-enable mongo

# fix cache file permissions
RUN rm -rf /opt/slack-api-export/var/cache

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN [ "/usr/bin/composer", "install", "-d /opt/slack-api-export" ]