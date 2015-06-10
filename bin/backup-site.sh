#!/bin/bash
# Settings
ABS_PATH=$(cd `dirname "${BASH_SOURCE[0]}"` && pwd)
CONTENT=$1
DATE=`date +%F`
# Define how many copies to keep
SAVECOUNT=14
# Set the target for an RSYNC, if no target is specified it
# doesn't RSYNC
#RSYNCTARGET=

if [ ! -f ${ABS_PATH}/database.auth ] 
then
 echo "ERROR: Unable to find ${ABS_PATH}/database.auth file, please create it and try again"
 exit 1
fi

if [ ! -d ${ABS_PATH}/backup ]
then
 echo "ERROR: Directory ${ABS_PATH}/backup doesn't exist please create it"
 exit 1
fi

if [ ! -d ${CONTENT} ]
then
 echo "ERROR: Unable to find content directory ${CONTENT}"
 exit 1
fi

echo "Dumping MySQL Database..."
mysqldump --defaults-extra-file=${ABS_PATH}/database.auth --add-drop-table --allow-keywords archie > ${ABS_PATH}/backup/${DATE}.archie.mysql
tar -C ${ABS_PATH}/backup -cjf ${ABS_PATH}/backup/${DATE}.archie.mysql.bz2 ${DATE}.archie.mysql
BCK_SIZE=`stat -c%s ${ABS_PATH}/backup/${DATE}.archie.mysql.bz2`
echo "MySQL Database backed up to ${ABS_PATH}/backup/${DATE}.archie.mysql.bz2 ${BCK_SIZE} bytes backed up"
rm -f ${ABS_PATH}/backup/${DATE}.archie.mysql
echo "Backing up Archie Content..."
tar -C ${CONTENT} -cjf ${ABS_PATH}/backup/${DATE}.archie.img.bz2 ${CONTENT}/*
BCK_SIZE=`stat -c%s ${ABS_PATH}/backup/${DATE}.archie.img.bz2`
echo "Content backed up to ${ABS_PATH}/backup/${DATE}.archie.img.bz2 ${BCK_SIZE} bytes backed up"
echo "Clearing Backups older than ${SAVECOUNT} days"
/usr/bin/find ${ABS_PATH}/backup/ -type f -mtime +${SAVECOUNT} -exec /bin/rm -rvf {} \;
if [ -n "$RSYNCTARGET" ]; then
  echo "RSYNC Target ${RSYNCTARGET} found, starting RSYNC"
  rsync -avz --delete-delay ${ABS_PATH}/backup/ ${RSYNCTARGET}
else
  echo "No RSYNC Target found, exiting"
fi
exit 0
