#!/bin/bash
sudo -u webapp php artisan cache:clear
sudo -u webapp php artisan config:cache
