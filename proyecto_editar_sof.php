<?
include "autenticacion.php";
// ejecuta autenticacion antes que nada
  autenticar_usuario();
  
require "xtpl.php";
include "config.php";
include "common/def_spa.php";
include "common/common_error.php";
//--------------------------------------------------------------------------
// proyecto_editar_sof.php
//
// Genera el formulario de relaciones del proyecto en cuestion. Para ello
// consulta las tablas de relacion con software.
//
// Parametros de entrada
//   idp : Identidad del proyecto
//--------------------------------------------------------------------------

  // definicion de Config usados
  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;

  // definicion de globales miembro
  // $pry_proyecto_desc -- mensaje de proyecto desconocido

  // coge el primero de la lista
  reset($gen_idiomas_disp);
  $idioma =  key($gen_idiomas_disp);

  // crea parser de la página
  $pagina=new XTemplate ("templates/es/proyecto_editar_sof.html");
  
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
 // OBTIENE/VERIFICA ID DE PROYECTO
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, sin modificar valores
 if (strlen($_POST['idp']) > 0) // 
 {
   $id_proyecto = $_POST['idp'];
 } 
 // para el caso de un enlace a la página de editar
 else
 {
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
 if ($_POST['modificado'])
 {
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
 
 while ($software = mysql_fetch_row($resultado))
 {
    // verifica si lo tengo que insertar en la lista de incluidos
    if ($array_software[$software[0]] == 1)
    {
      $num_soft_incluidos = $num_soft_incluidos + 1;
      // asigna valores a lista
      $lista_valores = array(
             'INDICE' => $num_soft_incluidos,
             'IDENTIFICADOR' => $software[0],
             'TITULO' => $software[1]);
      // insertalo en página
      $pagina->assign('LISTA',$lista_valores);
      $pagina->parse("main.form_proyecto.fila_software");
    }
    // o en el select de no incluidos
    else
    {
       // asigna valores
       $lista = array ( 'IDS' => $software[0],
                        'TITULO' => $software[1]);
       // imprimelos
       $pagina->assign('LISTA', $lista);
       $pagina->parse("main.form_proyecto.selec_software");      
    } 
 }
     
 // imprime los valores en página
 $pagina->assign("IDP",$id_proyecto);
 $pagina->assign("NUM_PAQUETES",$num_soft_incluidos);
 $pagina->parse("main.form_proyecto");

 //imprime resultado
 $pagina->parse("main");
 $pagina->out("main"); 

  // cierra descriptor
  mysql_close($conexion);
?>