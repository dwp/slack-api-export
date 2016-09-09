#!/bin/bash

rm -rf /opt/slack-api-export/var/cache/*
rm -rf /opt/slack-api-export/var/logs/*

export SYMFONY_ENV=prod

composer install --no-interaction --no-ansi

/bin/bash -l -c "./bin/console $*"