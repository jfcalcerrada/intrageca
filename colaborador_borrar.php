<?
include "autenticacion.php";
// ejecuta autenticacion antes que nada
  autenticar_usuario();
  
require "xtpl.php";
include "config.php";
include "common/def_spa.php";
include "common/common_error.php";
//--------------------------------------------------------------------------
// colaborador_borrar.php
//
// borra todas las entradas que hay en la base de datos referentes
// a un proyecto.
//
// Parametros de entrada
//   idp: El identificador del proyecto a borrar.
//   
//--------------------------------------------------------------------------

  // definicion de Config usados
  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;

  // chequea que el parametro de entrada está definido
  if (!isset($_GET['idc']) OR strlen($_GET['idc'])==0)
  {
     ERR_muestra_pagina_error("Identificador de colaborador no válido","");
     exit;          
  }

  // conecta a Base de Datos MySQL
  $conexion = mysql_connect("localhost",$USER_BD,$PASS_BD);
  // verifica si se abrió conexion
  if (!$conexion)
  {
     ERR_muestra_pagina_error($gen_error_conexion, "");
     return;
  }

  // selecciona base de datos
  mysql_select_db($BASE_DATOS,$conexion);  

 //--------------------------------------------------------------------
 // BORRA DE LAS TABLAS DEL PROYECTO
 //-------------------------------------------------------------------- 
 $set_autocommit = "SET AUTOCOMMIT=";
 $consultas[1] = 'DELETE FROM colaborador_proyectos, colaboradores USING '.
          'colaborador_proyectos, colaboradores WHERE '.
          'colaborador_proyectos.id_colaborador=colaboradores.id_colaborador '.
          'AND grupo_pertenece='.$_GET['idc'];
 $consultas[2] ="DELETE FROM grupos_colaboradores WHERE id_grupo=".$_GET['idc'];
 $consultas[3] ="COMMIT";// los cambios solo se hacen al final, si no hubo error
  
 // desactiva autocommit
 $resultado=mysql_query($set_autocommit."0", $conexion);
 
 if (!$resultado)
 {
        ERR_muestra_pagina_error("No se pudo eliminar el colaborador." & 
         mysql_error(),"");
        exit;          
 }
 
 // realiza las consultas de borrado de miembro
 for ($i=1;$i<4;$i++)
 {
    $resultado=mysql_query($consultas[$i], $conexion);

    if (!$resultado)
    {
        ERR_muestra_pagina_error("No se pudo eliminar el colaborador." & 
                                  mysql_error(),"");
        exit;          
    }
 }
 // vuelve a activar el autocommit (¿Realmente es necesario?)
 $resultado=mysql_query($set_autocommit."1", $conexion);
 
 ERR_muestra_pagina_mensaje("Se ha eliminado el grupo colaborador.", "");
   
 // cierra descriptor
 mysql_close($conexion);
    
?>