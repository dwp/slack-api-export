#!/bin/bash

echo ${SYMFONY__MONGODB_SERVER}

rm -rf /opt/slack-api-export/var/cache/*
rm -rf /opt/slack-api-export/var/logs/*

composer install

/bin/bash -l -c "$*"