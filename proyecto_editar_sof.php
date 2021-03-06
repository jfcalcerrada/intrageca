<?php

require_once 'common/init.php';

require_once 'common/proyectos.php';

// ejecuta autenticacion antes que nada
autenticar_usuario();

//--------------------------------------------------------------------------
// proyecto_editar_sof.php
//
// Genera el formulario de relaciones del proyecto en cuestion. Para ello
// consulta las tablas de relacion con software.
//
// Parametros de entrada
//   idp : Identidad del proyecto
//--------------------------------------------------------------------------

 //--------------------------------------------------------------------
 // OBTIENE/VERIFICA ID DE PROYECTO
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, sin modificar valores
if (isset($_POST['idp']) && strlen($_POST['idp']) > 0) { //
   $id_proyecto = $_POST['idp'];
}
// para el caso de un enlace a la p�gina de editar
else {
   $id_proyecto = $_GET['idp'];
}
 
 // verifica que tras identificacion, tenemos un identificado valido
if ((strlen($id_proyecto)==0) || ($id_proyecto == 0)) {
    ERR_muestra_pagina_error($pry_proyecto_desc, "");
    exit;
}
 //--------------------------------------------------------------------
 // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
 //--------------------------------------------------------------------
 if (isset($_POST['modificado']) && $_POST['modificado']) {
   // verifica si tenemos que borrar algun miembro
   for ($i=1; $i<=$_POST['numero_paquetes']; $i++)
   {
      if (($_POST["id_soft_b_$i"] == 1) && ($_POST["id_soft_i_$i"]>0 ))
      {
         // prepara consulta de borrado
         $consulta_borrar = "DELETE FROM software_proyectos WHERE id_proyecto=".
                        $id_proyecto." AND id_software=".$_POST["id_soft_i_$i"];
     	   // ejecuta consulta
     	   $resultado = mysql_query($consulta_borrar, $conexion);
         // pon mensaje de aviso si no fue bien
         if (!$resultado)
         {
      	    $mensaje_aviso = "No se pudo borrar el registro ".
      	                      $_POST["id_soft_i_$i"]."\n"; 
         }           
      }
   }
   // verifica si hay que insertar algun miembro
   if ((strlen($_POST['nuevo_software'])>0) && ($_POST['nuevo_software']>0))
   {
      // consulta de insercion
      $consulta_insert = "INSERT INTO software_proyectos(id_proyecto,".
        "id_software) VALUES(".$id_proyecto.",".$_POST['nuevo_software'].")";
     	// ejecuta consulta
     	$resultado = mysql_query($consulta_insert, $conexion);
      // pon mensaje de aviso si no fue bien
      if (!$resultado)
      {
          $mensaje_aviso .= "No se pudo insertar el registro ".
                            $_POST["nuevo_software"]."\n"; 
      }        
   }
   
 }


// Muestra el submen�, y si es el administrador el bot�n de borrar
$_content = menu_proyectos($_content, $id_proyecto);


 //--------------------------------------------------------------------
 // OBTIENE LOS PAQUETES DEL PROYECTO DE LA BASE DE DATOS
 //--------------------------------------------------------------------
 $array_software = array();

 // obtiene software que pertenecen al proyecto
 $consulta_rel = 'SELECT id_software FROM software_proyectos WHERE '.
                 'id_proyecto='.$id_proyecto;
                 
 // ejecuta la consulta para obtener datos
 $resultado = mysql_query($consulta_rel, $conexion);
 
 if (!$resultado)
 {
     echo "Error al realizar la consulta ".$consulta_rel;
 } 
 else
 { 
   while($id_soft = mysql_fetch_row($resultado))
   {
      $array_software[$id_soft[0]] = 1;
   }
 }
 mysql_free_result($resultado);
  
 // obtiene la lista de paquetes que estan en el proyecto
 $consulta_todos = 'SELECT id_software, titulo FROM software_idiomas'.
     ' WHERE idioma="'.$idioma.'"';
    
 // ejecuta la consulta para obtener datos
 $resultado = mysql_query($consulta_todos, $conexion);
 
 if (!$resultado)
 {
     echo "Error al realizar la consulta ".$consulta_todos;
 } 
 
 $num_soft_incluidos = 0;
 
while ($software = mysql_fetch_row($resultado))  {
    // verifica si lo tengo que insertar en la lista de incluidos
    if (isset($array_software[$software[0]])) {
      $num_soft_incluidos = $num_soft_incluidos + 1;
      // asigna valores a lista
      $lista_valores = array(
             'INDICE' => $num_soft_incluidos,
             'IDENTIFICADOR' => $software[0],
             'TITULO' => $software[1]);
      // insertalo en p�gina
      $_content->assign('LISTA',$lista_valores);
      $_content->parse("content.fila_software");
    }
    // o en el select de no incluidos
    else {
       // asigna valores
       $lista = array ( 'IDS' => $software[0],
                        'TITULO' => $software[1]);
       // imprimelos
       $_content->assign('LISTA', $lista);
       $_content->parse("content.selec_software");
    }
}
     
// imprime los valores en p�gina
$_content->assign("IDP",$id_proyecto);
$_content->assign("NUM_PAQUETES",$num_soft_incluidos);


// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
