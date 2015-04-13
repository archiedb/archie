#!/bin/bash
# Settings
ABS_PATH=$(cd `dirname "${BASH_SOURCE[0]}"` && pwd)
#ABS_PATH=$(cd `dirname "${BASH_SOURCE[0]}"` && pwd)/`basename "${BASH_SOURCE[0]}"`
CONTENT=$ARGV[0]
DATE=`date +%F`

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
echo "Backing up Archie Contact..."
tar -C ${CONTENT} -cjf ${ABS_PATH}/backup/${DATE}.archie.img.bz2 ${CONTENT}/*
BCK_SIZE=`stat -c%s ${ABS_PATH}/backup/${DATE}.archie.img.bz2`
echo "Content backed up to ${ABS_PATH}/backup/${DATE}.archie.img.bz2 ${BCK_SIZE} bytes backed up"
exit 0
