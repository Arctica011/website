#!/bin/bash
MIGRATE_DB=$(sudo awk -F "=" '/MIGRATE_DB/ {print $2}' /opt/elasticbeanstalk/deployment/env)
if [ "$MIGRATE_DB" == "true" ]; then
   sudo -u webapp php artisan migrate
fi


