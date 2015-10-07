#! /bin/bash
#
# Script to restore backup
#
CVSREPO=/var/cvs/
BASE_DATOS=webgeca_prod
BACKUP_USER=backup_user
BACKUP_USER_PASSWD=backup_user_6RF
TMPDIR=/tmp
BD=WEBGECA/intrageca/bd
BACKUP_DIR=${TMPDIR}/${BD}
BACKUP_FILE=backup_$BASE_DATOS.sql
#
# Get last CVS version
echo "Restoring database"
cd ${TMPDIR}
cvs -d $CVSREPO co ${BD}
cd $BACKUP_DIR
rm $BACKUP_FILE
cvs update $BACKUP_FILE
# Insert in DB
mysql -u $BACKUP_USER -p$BACKUP_USER_PASSWD < $BACKUP_FILE
cd ${TMPDIR}
rm -fR ${BD%%/*}

exit 0