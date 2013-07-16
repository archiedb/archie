#!/bin/bash
# Settings
BIN="/archie/bin"
CONTENT="/archie/content"
DATE=`date +%F`

mysqldump --defaults-extra-file=${BIN}/database.auth --add-drop-table --allow-keywords archie > ${BIN}/backup/${DATE}.archie.mysql
tar -C ${BIN}/backup -cjf ${BIN}/backup/${DATE}.archie.mysql.bz2 ${DATE}.archie.mysql
rm -f ${BIN}/backup/${DATE}.archie.mysql
tar -C ${CONTENT} -cjf ${BIN}/backup/${DATE}.archie.img.bz2 ${CONTENT}/*
