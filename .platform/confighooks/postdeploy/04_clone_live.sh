#!/bin/bash
CLONE_DB=$(sudo awk -F "=" '/CLONE_DB/ {print $2}' /opt/elasticbeanstalk/deployment/env)
if [ "$CLONE_DB" == "true" ]; then
    sudo -u webapp php artisan db:clone;
fi
