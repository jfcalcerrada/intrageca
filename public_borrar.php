<?php
// Inicializamos el archivo con el script
include('common/init.php');


//--------------------------------------------------------------------------
// public_borrar.php
//
// Borra el identificador de referencia que se le pasa por parametro
// de la base de datos.
// 
// Los parámetros que necesita la página son:
//
//   id_ref: Identificador del registro a editar
//--------------------------------------------------------------------------

  // definicion de Config usados
  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;

  // verifica si id_ref está definido, si no lo está, crea página
  // de error
  if (!isset($_POST['id_ref']) OR strlen($_POST['id_ref'])==0)
  {
     ERR_muestra_pagina_error("Identificador de referencia no válido", "");
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

//--------------------------------------------------------------
//  REALIZA EL BACKUP DE LAS REFERENCIAS
//---------------------------------------------------------------


//--------------------------------------------------------------
//  EJECUTA CONSULTAS DE BORRADO
//---------------------------------------------------------------
  // consulta los campos que son responsabilidad directa
  $consulta_campos = 'SELECT id_campos FROM ref_relacion '.
   'WHERE referencia_cruzada=0 AND id_ref='.$_POST['id_ref'];
   
  $resultado=mysql_query($consulta_campos, $conexion);
  
  $id_campos = mysql_fetch_row($resultado);
  
  // define consultas de borrado
  $consultas_borrar = array( 
     "DELETE FROM ref_campos WHERE id_campo_ref=".$id_campos[0],
     "DELETE FROM ref_relacion WHERE id_ref=".$_POST['id_ref']." OR ".
      "id_ref_cruzada=".$_POST['id_ref'],
     "DELETE FROM referencias WHERE id_referencia=".$_POST['id_ref']);
  
  // ejecuta todas las consultas
  foreach ($consultas_borrar as $consulta)
  {

     // ejecuta consulta
     $resultado=mysql_query($consulta, $conexion);
     
     // chequea si ha habido error
     if (!$resultado)
     {
       ERR_muestra_pagina_error("No se pudo borrar registro.".
          " Error de consulta: ".$consulta, "");
       exit;         
     }
  }
  // cierra descriptor
  mysql_close($conexion);

  // muestra mensaje de todo OK
  ERR_muestra_pagina_mensaje("Se ha eliminado la referencia.", "");


?>