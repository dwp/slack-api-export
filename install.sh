#!/bin/bash

# Install composer in to local user exec directory on OSX
echo "Installing Composer via homebrew."
brew install composer

# Install package dependencies
cd src
composer install --no-interaction --no-ansi

# and run docker-compose
docker-compose up