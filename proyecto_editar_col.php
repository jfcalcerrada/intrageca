<?php

require_once 'common/init.php';

require_once 'common/proyectos.php';

// ejecuta autenticacion antes que nada
autenticar_usuario();

//--------------------------------------------------------------------------
// proyecto_editar_col.php
//
// Genera el formulario de relaciones del proyecto en cuestion. Para ello
// consulta las tablas de relacion con colaboradores.
//
// Parametros de entrada
//   idp : Identidad del proyecto
//--------------------------------------------------------------------------

//--------------------------------------------------------------------
// OBTIENE/VERIFICA ID DE PROYECTO
//--------------------------------------------------------------------
// le hemos dado a actualizar al formulario, sin modificar valores
if (isset($_POST['idp']) && strlen($_POST['idp']) > 0) {
    $id_proyecto = $_POST['idp'];
} else{ // para el caso de un enlace a la página de editar
    $id_proyecto = $_GET['idp'];
}

 // verifica que tras identificacion, tenemos un identificado valido
 if ((strlen($id_proyecto)==0) || ($id_proyecto == 0))
 {
        ERR_muestra_pagina_error($pry_proyecto_desc, "");
        exit;     
 }  
 //--------------------------------------------------------------------
 // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
 //--------------------------------------------------------------------
 if (isset($_POST['modificado']) && $_POST['modificado']) {

   // verifica si tenemos que borrar algun miembro
   for ($i = 1; $i <= $_POST['numero_col']; $i++) {
      if (($_POST["id_proy_b_$i"] == 1) && ($_POST["id_proy_i_$i"]>0 ))
      {
         // prepara consulta de borrado
         $consulta_borrar = "DELETE FROM colaborador_proyectos ".
                         "WHERE id_proyecto=".$id_proyecto.
                         " AND id_colaborador=".$_POST["id_proy_i_$i"];
     	   // ejecuta consulta
     	   $resultado = mysql_query($consulta_borrar, $conexion);
         // pon mensaje de aviso si no fue bien
         if (!$resultado)
         {
      	    $mensaje_aviso = "No se pudo borrar el registro ".
      	                      $_POST["id_bibtex_i_$i"]."\n"; 
         }           
      }
   }
   // verifica si hay que insertar algun miembro
   if ((strlen($_POST['nuevo_col'])>0) && ($_POST['nuevo_col']>0))
   {
      // consulta de insercion
      $consulta_insert = "INSERT INTO colaborador_proyectos(id_proyecto,".
        "id_colaborador) VALUES(".$id_proyecto.",".$_POST['nuevo_col'].")";
     	// ejecuta consulta
     	$resultado = mysql_query($consulta_insert, $conexion);
      // pon mensaje de aviso si no fue bien
      if (!$resultado)
      {
          $mensaje_aviso .= "No se pudo insertar el registro ".
                            $_POST["nuevo_col"]."\n"; 
      }        
   }
 }

// Muestra el submenú, y si es el administrador el botón de borrar
$_content = menu_proyectos($_content, $id_proyecto);

//--------------------------------------------------------------------
// OBTIENE LOS COLABORADORES DEL PROYECTO DE LA BASE DE DATOS
//--------------------------------------------------------------------
$lista_col_incluidos = array();
// obtiene colaboradores que pertenecen al proyecto
$consulta_rel = 'SELECT id_colaborador FROM colaborador_proyectos WHERE '.
             'id_proyecto='.$id_proyecto;

// ejecuta la consulta para obtener datos
$resultado = mysql_query($consulta_rel, $conexion);

if (!$resultado) {
 echo "Error al realizar la consulta ".$consulta_rel;

} else {
    while($id_col = mysql_fetch_row($resultado)) {
        $lista_col_incluidos[$id_col[0]] = 1;
    }
}

 mysql_free_result($resultado);
 
// obtiene la lista de colaboradores que estan en el proyecto
$consulta_todos = 'SELECT id_colaborador, nombre, nombre_grupo FROM '.
    'colaboradores LEFT JOIN grupos_colaboradores '.
    'ON colaboradores.grupo_pertenece=grupos_colaboradores.id_grupo '.
    'ORDER BY nombre_grupo ASC';

// ejecuta la consulta para obtener datos
$resultado = mysql_query($consulta_todos, $conexion);
 
if (!$resultado) {
    echo "Error al realizar la consulta ".$consulta_todos;
}
 
$num_col_incluidos = 0;
while ($colaborador = mysql_fetch_row($resultado)) {

    // verifica si lo tengo que insertar en la lista de incluidos
    if (isset($lista_col_incluidos[$colaborador[0]])) {
      $num_col_incluidos = $num_col_incluidos + 1;
      // asigna valores a lista
      $lista_valores = array(
             'INDICE' => $num_col_incluidos,
             'IDENTIFICADOR' => $colaborador[0],
             'NOMBRE' => $colaborador[1],
             'GRUPO_COL' => $colaborador[2]);
      // insertalo en página
      $_content->assign('LISTA',$lista_valores);
      $_content->parse("content.fila_colaborador");
    }
    // o en el select de no incluidos
    else {
       // asigna valores
       $lista = array ( 'IDC' => $colaborador[0],
                        'NOMBRE' => $colaborador[2]."/".$colaborador[1]);
       // imprimelos
       $_content->assign('LISTA', $lista);
       $_content->parse("content.selec_col");
    } 
}


if (isset($mensaje_aviso)) {
    $_content->assign('MENSAJE_ACTUALIZACION', $mensaje_aviso);
}

// imprime los valores en página
$_content->assign("IDP",$id_proyecto);
$_content->assign("NUM_COL",$num_col_incluidos);
$_content->parse("content.form_proyecto");

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
