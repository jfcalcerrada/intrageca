<?php

require_once 'common/init.php';

//--------------------------------------------------------------------------
// proyecto_borrar.php
//
// borra todas las entradas que hay en la base de datos referentes
// a un proyecto.
//
// Parametros de entrada
//   idp: El identificador del proyecto a borrar.
//   
//--------------------------------------------------------------------------


//--------------------------------------------------------------------
// BORRA DE LAS TABLAS DEL PROYECTO
//-------------------------------------------------------------------- 
$set_autocommit = "SET AUTOCOMMIT=";
$consultas[1] = "DELETE FROM proyecto_miembros WHERE id_proyecto=".$_GET['idp'];
$consultas[2] = "DELETE FROM colaborador_proyectos WHERE id_proyecto=".$_GET['idp'];
$consultas[3] = "DELETE FROM software_proyectos WHERE id_proyecto=".$_GET['idp'];
$consultas[4] = "DELETE FROM proyecto_idiomas WHERE id_proyecto=".$_GET['idp'];
$consultas[5] = "DELETE FROM proyectos WHERE id_proyecto=".$_GET['idp'];
$consultas[6] = "COMMIT";// los cambios solo se hacen al final, si no hubo error

// desactiva autocommit
$resultado=mysql_query($set_autocommit."0", $conexion);

if (!$resultado)
{
    ERR_muestra_pagina_error("No se pudo eliminar el proyecto." &
        mysql_error(), "");
    exit;
}

// realiza las consultas de borrado de miembro
for ($i=1;$i<7;$i++)
{
    $resultado=mysql_query($consultas[$i], $conexion);

    if (!$resultado)
    {
        ERR_muestra_pagina_error("No se pudo eliminar el proyecto." &
            mysql_error(), "");
        exit;
    }
}
// vuelve a activar el autocommit (¿Realmente es necesario?)
$resultado=mysql_query($set_autocommit."1", $conexion);

ERR_muestra_pagina_mensaje("Se ha eliminado el proyecto del grupo.", "");

// cierra descriptor
mysql_close($conexion);
