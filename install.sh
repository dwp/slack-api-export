#!/bin/bash

# check .env is setup
if [ ! -f '.env' ]; then
    echo "docker-compose .env file is not found, please complete project setup before running install.sh"
    exit
fi

# Install composer in to local user exec directory on OSX
echo "Installing Composer via homebrew."
brew install composer

# Install package dependencies
composer install --no-interaction --no-ansi -d ./src

# and run docker-compose
docker-compose up