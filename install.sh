#!/bin/bash

# check .env is setup
if [ ! -f '.env' ]; then
    echo "docker-compose .env file is not found, please complete project setup before running install.sh"
    exit
fi

# and run docker-compose
docker-compose up --build