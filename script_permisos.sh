#!/bin/bash
# ###############################################################
# $Id: script_permisos.sh,v 1.2 2009/10/20 09:17:32 pfcweb3 Exp $
# ################################################################
#
# script para dar permisos adecuados a copia de trabajo tras un 
# checkout o update

#   basicamente es dar permisos de escritura por grupo 
#   www-data a algunos directorios)
#
# ################################################################

#set -o xtrace
#set -o noglob

DIRS="docs fotos"

# cambiar grupo 
echo " Cambiando grupo en ${DIRS}..."
find $DIRS   |grep  -v '/CVS'| xargs   chgrp www-data  

# cambiar permisos de escritura 
echo " Cambiando permisos en ${DIRS}..."
find docs fotos   |grep  -v '/CVS'| xargs  chmod  g+w 