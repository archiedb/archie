#!/bin/bash
# Settings
BIN="/archie/bin"
CONTENT="/archie/content"
DATE=`date +%F`

if [ ! -f ${BIN}/database.auth ] 
then
 echo "ERROR: Unable to find ${BIN}/database.auth file, please create it and try again"
 exit 1
fi

if [ ! -d ${BIN}/backup ]
then
 echo "ERROR: Directory ${BIN}/backup doesn't exist please create it"
 exit 1
fi

if [ ! -d ${CONTENT} ]
then
 echo "ERROR: Unable to find content directory ${CONTENT}"
 exit 1
fi

mysqldump --defaults-extra-file=${BIN}/database.auth --add-drop-table --allow-keywords archie > ${BIN}/backup/${DATE}.archie.mysql
tar -C ${BIN}/backup -cjf ${BIN}/backup/${DATE}.archie.mysql.bz2 ${DATE}.archie.mysql
rm -f ${BIN}/backup/${DATE}.archie.mysql
tar -C ${CONTENT} -cjf ${BIN}/backup/${DATE}.archie.img.bz2 ${CONTENT}/*
echo "MySQL Database backed up to ${BIN}/backup/${DATE}.archie.img.bz2"
