#!/bin/bash

## This scripts intent is just to set the ../lib/cache directory to owned by the webserver user
## We'll try to guess what the user is based on what OS this is, default to thinking it's debian
## Cause that makes it easier for us

# Really right now this is just completely broken
chown www-data.www-data /var/www/lib/cache
chmod 775 /var/www/lib/cache
