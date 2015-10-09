<?php

require_once 'common/init.php';

// Autenticamos al usuario
autenticar_usuario();

require_once "software_insertar.php";

//--------------------------------------------------------------------------
// software_editar.php
//
// Genera el formulario de un software en cuestion. Para ello necesita que
// se le pase como parámetro la identidad de software. Si la identidad
// no está disponible, se muestra la página de error.
//
// Parametros de entrada
//   ids : Identidad del software
//--------------------------------------------------------------------------
  
 //--------------------------------------------------------------------
 // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, modificando valores
if (isset($_POST['modificado']) && $_POST['modificado'] == 1) {
   // llama a la funcion de insertar/actualizar
   $id_software = software_insertar($conexion, $_POST);
}
 // le hemos dado a actualizar al formulario, sin modificar valores
elseif (isset($_POST['ids']) && strlen($_POST['ids']) > 0) {
   $id_software = $_POST['ids'];
}
 // para el caso de un enlace a la página de editar
else {
   $id_software = $_GET['ids'];
}
 
 // verifica que tras identificacion, tenemos un identificado valido
if (strlen($id_software) == 0) {
    ERR_muestra_pagina_error("Software desconocido", "");
    exit;
}

// Seleccionamos el idioma en que se esta mostrando el
if (isset($_POST['idioma']) && strlen($_POST['idioma'])
    && array_key_exists($_POST['idioma'], $gen_idiomas_disp)
) {
    $idioma = $_POST['idioma'];
} else {
    $idioma = $_lang;
}


 //--------------------------------------------------------------------
 // OBTIENE LOS VALORES DEL SOFTWARE DE LA BASE DE DATOS
 //--------------------------------------------------------------------
 if ($id_software > 0) {
   // crea la consulta del software
   $consulta_software = 
     ' SELECT sistema_operativo, licencia, link_licencia, id_sw_bibtex, '.
     ' email_soporte, link_homepage, publico '.
     ' FROM software WHERE software.id_software='.$id_software;
  
   // ejecuta la consulta para obtener datos
   $resultado = mysql_query($consulta_software, $conexion);
   
   if ($resultado) {
        $registros = mysql_fetch_row($resultado);
   } else {
        echo "Error al realizar la consulta ".$consulta_software;
   }

   // ejecuta la consulta dependiente de idiomas
   $consulta_idiomas = 'SELECT titulo, descrip_corta, descripcion'.
     ' FROM software_idiomas '.
     ' WHERE idioma="' . $idioma . '" AND id_software='.$id_software;

   // ejecuta la consulta para obtener datos
   $resultado = mysql_query($consulta_idiomas, $conexion);
   
   if ($resultado) {
       $ids_registros = mysql_fetch_row($resultado);
   } else {
       echo "Error al realizar la consulta ".$consulta_idiomas;
   }
   
 }
 
 //--------------------------------------------------------------------
 // RELLENA FORMULARIO DE BORRADO Y RELACIONES SI NO ES NUEVO
 //-------------------------------------------------------------------- 
 if ($id_software != 0) {
  $_content->assign("IDS", $id_software);
  $_content->parse("content.form_borrar");
  $_content->assign("IDS", $id_software);
  $_content->parse("content.form_software.acceso_relaciones");  
 }

 //--------------------------------------------------------------------
 // RELLENA LOS VALORES DE SOFTWARE
 //--------------------------------------------------------------------   
 // imprime los idiomas disponibles
 foreach($gen_idiomas_disp as $cod => $texto_idioma) {
     // selecciona elementos
     $selected = ($idioma == $cod) ? "SELECTED" : "";

     // asigna lista
     $lista_idioma = array ( 'COD_IDIOMA' => $cod,
                             'IDIOMA'     => $texto_idioma,
                             'SELECTED'   => $selected);
     // insertalo en página
     $_content->assign('LISTA',$lista_idioma);
     $_content->parse('content.form_software.fila_idioma');
 } 
 $publicar = ($registros[6] == 1)? "CHECKED":"";
  
 // rellena el resto de valores normales
  $lista_valores = array(
         'IDS' => $id_software,
         'TITULO' => $ids_registros[0],
         'DESC_CORTA' => $ids_registros[1],
         'DESCRIPCION' => $ids_registros[2],
         'SIST_OPER' => $registros[0],
         'LICENCIA' => $registros[1],
         'LINK_LICENCIA' => $registros[2],
         'ID_BIBTEX' => $registros[3],
         'EMAIL' => $registros[4],
         'HOMEPAGE' => $registros[5],
         'PUBL' => $publicar); 
  
  // imprime los valores en página
  $_content->assign("LISTA",$lista_valores);
  $_content->parse("content.form_software");

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
