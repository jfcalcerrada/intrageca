#!/bin/bash
#
# Script to make backup
#
CVSREPO=/var/cvs/
BASE_DATOS=webgeca_prod
BACKUP_USER=backup_user
BACKUP_USER_PASSWD=backup_user_6RF
TMPDIR=/tmp
BD=WEBGECA/intrageca/bd
BACKUP_DIR=${TMPDIR}/${BD}
BACKUP_FILE=backup_$BASE_DATOS.sql
FECHA=`date`
#
# Create file with information
echo "Making backup of database"
cd $TMPDIR
cvs -d $CVSREPO co ${BD}
cd $BACKUP_DIR
rm $BACKUP_FILE
echo "drop database $BASE_DATOS;" > $BACKUP_FILE
echo "create database $BASE_DATOS;" >> $BACKUP_FILE
echo "use $BASE_DATOS;" >> $BACKUP_FILE
mysqldump --lock-tables --disable-keys -u $BACKUP_USER -p$BACKUP_USER_PASSWD $BASE_DATOS >> $BACKUP_FILE
#
# Insert file in CVS
cvs ci -m"$FECHA" $BACKUP_FILE
cd ${TMPDIR}
rm -fR ${BD%%/*}

exit 0 
