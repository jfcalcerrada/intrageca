<?php
// Inicializamos el archivo con el script
include("common/init.php");
include("autenticacion.php");
// Autenticamos al usuario
autenticar_usuario();

//--------------------------------------------------------------------------
// software_borrar.php
//
// borra todas las entradas que hay en la base de datos referentes
// a un software.
//
// Parametros de entrada
//   idp: El identificador del proyecto a borrar.
//   
//--------------------------------------------------------------------------

  // definicion de Config usados
  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;
  global $software_dir_paquetes;

  // chequea que el parametro de entrada está definido
  if (!isset($_GET['ids']) OR strlen($_GET['ids'])==0)
  {
     ERR_muestra_pagina_error("Identificador de software no válido", "");
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
 // BORRA EL DIRECTORIO QUE CONTIENE LOS PAQUETES
 //-------------------------------------------------------------------- 
  // abre directorio de software
  $directorio= opendir($software_dir_paquetes.'paq_'.$_GET['ids']);
  // read . and ..
  readdir($directorio);
  readdir($directorio);
  // para cada entrada, borra el fichero
  while ($fichero = readdir($directorio))
  {
    unlink($software_dir_paquetes.'paq_'.$_GET['ids'].'/'.$fichero);
  }
  // cierra directorio
  closedir($directorio);
  // borrar directorio
  rmdir($software_dir_paquetes.'paq_'.$_GET['ids']);

 //--------------------------------------------------------------------
 // BORRA DE LAS TABLAS DEL PROYECTO
 //-------------------------------------------------------------------- 
 $set_autocommit = "SET AUTOCOMMIT=";
 $consultas[1] = "DELETE FROM software_proyectos WHERE id_software=".$_GET['ids'];
 $consultas[2] = "DELETE FROM paquetes_software WHERE id_software=".$_GET['ids'];
 $consultas[3] = "DELETE FROM software_idiomas WHERE id_software=".$_GET['ids'];
 $consultas[4] = "DELETE FROM software WHERE id_software=".$_GET['ids'];
 $consultas[5] = "COMMIT";// los cambios solo se hacen al final, si no hubo error
  
 // desactiva autocommit
 $resultado=mysql_query($set_autocommit."0", $conexion);
 
 if (!$resultado)
 {
        ERR_muestra_pagina_error("No se pudo eliminar el software." & 
          mysql_error(), "");
        exit;          
 }
 
 // realiza las consultas de borrado de miembro
 for ($i=1;$i<6;$i++)
 {
    $resultado=mysql_query($consultas[$i], $conexion);

    if (!$resultado)
    {
        ERR_muestra_pagina_error("No se pudo eliminar el software." & ç
          mysql_error(), "");
        exit;          
    }
 }
 // vuelve a activar el autocommit (¿Realmente es necesario?)
 $resultado=mysql_query($set_autocommit."1", $conexion);
 
 ERR_muestra_pagina_mensaje("Se ha eliminado el software del grupo.", "");
   
 // cierra descriptor
 mysql_close($conexion);
    
?>