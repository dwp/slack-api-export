#!/bin/bash

rm -rf /opt/slack-api-export/var/cache/*
rm -rf /opt/slack-api-export/var/logs/*

composer install --no-interaction --no-ansi

/bin/bash -l -c "$*"