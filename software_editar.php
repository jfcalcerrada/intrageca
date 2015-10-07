<?php
// Inicializamos el archivo con el script
include("common/init.php");
include("autenticacion.php");
// Autenticamos al usuario
autenticar_usuario();

include("software_insertar.php");

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

$pagina = $contenido;
  
 //--------------------------------------------------------------------
 // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, modificando valores
 if ($_POST['modificado'] == 1) 
 {
   // llama a la funcion de insertar/actualizar
   $id_software = software_insertar($conexion, $_POST);
 }
 // le hemos dado a actualizar al formulario, sin modificar valores
 else if (strlen($_POST['ids']) > 0) // 
 {
   $id_software = $_POST['ids'];
 } 
 // para el caso de un enlace a la página de editar
 else
 {
   $id_software = $_GET['ids'];
 }
 
 // verifica que tras identificacion, tenemos un identificado valido
 if (strlen($id_software)==0) 
 {
        ERR_muestra_pagina_error("Software desconocido", "");
        exit;     
 }  

 //--------------------------------------------------------------------
 // OBTIENE LOS VALORES DEL SOFTWARE DE LA BASE DE DATOS
 //--------------------------------------------------------------------
 if ($id_software >0)
 {   
   // crea la consulta del software
   $consulta_software = 
     ' SELECT sistema_operativo, licencia, link_licencia, id_sw_bibtex, '.
     ' email_soporte, link_homepage, publico '.
     ' FROM software WHERE software.id_software='.$id_software;
  
   // ejecuta la consulta para obtener datos
   $resultado = mysql_query($consulta_software, $conexion);
   
   if ($resultado)
     {$registros = mysql_fetch_row($resultado);}
   else
     {echo "Error al realizar la consulta ".$consulta_software;}

   // ejecuta la consulta dependiente de idiomas
   $consulta_idiomas = 'SELECT titulo, descrip_corta, descripcion'.
     ' FROM software_idiomas '.
     ' WHERE idioma="'.$idioma.'" AND id_software='.$id_software;  

   // ejecuta la consulta para obtener datos
   $resultado = mysql_query($consulta_idiomas, $conexion);
   
   if ($resultado)
     {$ids_registros = mysql_fetch_row($resultado);}
   else
     {echo "Error al realizar la consulta ".$consulta_idiomas;}     
   
 }
 
 //--------------------------------------------------------------------
 // RELLENA FORMULARIO DE BORRADO Y RELACIONES SI NO ES NUEVO
 //-------------------------------------------------------------------- 
 if ($id_software != 0)
 {
  $pagina->assign("IDS", $id_software);
  $pagina->parse("main.form_borrar");
  $pagina->assign("IDS", $id_software);
  $pagina->parse("main.form_software.acceso_relaciones");  
 }

 //--------------------------------------------------------------------
 // RELLENA LOS VALORES DE SOFTWARE
 //--------------------------------------------------------------------   
 // imprime los idiomas disponibles
 foreach($gen_idiomas_disp as $cod => $texto_idioma)
 {
     // selecciona elementos
     $selected = ($idioma==$cod)? "SELECTED":"";
     // asigna lista
     $lista_idioma = array ( 'COD_IDIOMA' => $cod,
                             'IDIOMA'     => $texto_idioma,
                             'SELECTED'   => $selected);
     // insertalo en página
     $pagina->assign('LISTA',$lista_idioma);
     $pagina->parse('main.form_software.fila_idioma');
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
  $pagina->assign("LISTA",$lista_valores);
  $pagina->parse("main.form_software");

 //imprime resultado
 $pagina->parse("main");
 $pagina->out("main"); 

?>