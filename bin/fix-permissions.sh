#!/bin/bash
ABS_PATH=$(cd `dirname "${BASH_SOURCE[0]}"` && pwd)
## This scripts intent is just to set the ../lib/cache directory to owned by the webserver user
## We'll try to guess what the user is based on what OS this is, default to thinking it's debian
## Cause that makes it easier for us

# Really right now this is just completely broken
chown www-data.www-data ${ABS_PATH}/../lib/cache
chmod 775 ${ABS_PATH}/../lib/cache
