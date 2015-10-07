<?php
// Inicializamos el archivo con el script
include("common/init.php");
include("autenticacion.php");
// Autenticamos al usuario
autenticar_usuario();

//-----------------------------------------------------------------------
// funcion: asigna_select_box
// 
// Esta funcion asigna a un select box de una página un rango incremental
// seleccionando aquel elemento indicado
//
function asigna_select_box ($inicio, $fin, $v_selec, &$pagina, $nom_fila)
{
  for ($ind=$inicio; $ind<=$fin; $ind++)
  {
     if ("$ind" == $v_selec) $selected = 'SELECTED';
     else $selected = '';
     // asigna a la página el elemento de la lista
     $pagina->assign('VAL',$ind);
     $pagina->assign('SELEC',$selected);
     $pagina->parse($nom_fila);                                          
  }   
}

//--------------------------------------------------------------------------
// software_editar_paq.php
//
// Genera el formulario de un software en cuestion. Para ello necesita que
// se le pase como parámetro la identidad de software. Si la identidad
// no está disponible, se muestra la página de error.
//
// Parametros de entrada
//   ids        : Identidad del software
//   numero_paq : El número de paquetes ya incluidos
//   nuevo_paq  : El nombre del nuevo paquete
//   pq_borrar_$i  : Borrar el paquete $i
//   pq_nombre_$i  : El nombre del paquete $i
//   pq_version_$i : La version del paquete $i
//   pq_id_sw_$i   : La id del paquete $i
//   pq_link_$i    : Enlace a fichero del paquete $i
//   pq_dia_$i, pq_mes_$i, pq_anyo_$i: Dia/Mes/Anyo del paquete de SW
//   
//--------------------------------------------------------------------------

   $pagina = $contenido;

 //--------------------------------------------------------------------
 // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, modificando valores
 if ($_POST['modificado'] == 1) 
 {
   //---------------------------------------
   // actualiza todos los presentes
   //---------------------------------------
   for ($i=1; $i<=$_POST['numero_paq'];$i++)
   {
      // verifica si hay que eliminar fichero anterior
      if ((($_POST["pq_borrar_$i"]==1) || 
          (strlen($HTTP_POST_FILES["pq_file_$i"]['name'])>0)) &&
          (strlen($_POST["pq_link_$i"])>0))
      {
         $fichero_anterior = $software_dir_paquetes.'paq_'.$_POST["ids"].'/'.
                   $_POST["pq_link_$i"];
         // si hay un fichero anterior, borralo
         if (file_exists($fichero_anterior))
         {
           unlink($fichero_anterior);    
         }         
      }
      // verifica si hay que borrar registro
      if ($_POST["pq_borrar_$i"]==1)
      { 
         // construye consulta de borrado
         $consulta_borrado = 'DELETE FROM paquetes_software '.
            'WHERE id_paq_soft='.$_POST["pq_id_sw_$i"];
         // ejecuta consulta
         $resultado = mysql_query($consulta_borrado, $conexion); 
         if (! $resultado)
         {
            echo "No se pudo ejecutar la consulta ".$consulta_borrado;
         }          
      } 
      // sino, actualiza los valores del registro y copiar nuevo fichero
      else
      {
         $condicion_link = "";
         // verifica si insertamos un fichero nuevo
         if (strlen($HTTP_POST_FILES["pq_file_$i"]['name'])>0) 
         {
            $nombre_local = $software_dir_paquetes."paq_".$_POST['ids']."/".
                            $HTTP_POST_FILES["pq_file_$i"]['name'];
                            
            // copia fichero a directorio de curriculum renombrandolo
            copy($HTTP_POST_FILES["pq_file_$i"]['tmp_name'],$nombre_local); 
            
            // construye link y condicion SQL
            $link = 'sw/paq_'.$_POST['ids'].'/'.
                      $HTTP_POST_FILES["pq_file_$i"]['name'];
            $condicion_link = ', link_software="'.$link.'"';
         } 
         
         // chequea los valores de entrada
         $nombre = addslashes($_POST["pq_nombre_$i"]);
         $version = addslashes($_POST["pq_version_$i"]);  
         $fecha = "'".$_POST["pq_anyo_$i"]."-".$_POST["pq_mes_$i"]."-".
                   $_POST["pq_dia_$i"]."'";
         
         // construye consulta de actualizacion
         $consulta_update = 'UPDATE paquetes_software SET nombre="'.
            $nombre.'", version="'.$version.'", fecha='.$fecha.
            $condicion_link.' WHERE id_paq_soft='.$_POST["pq_id_sw_$i"];
         // ejecuta consulta
         $resultado = mysql_query($consulta_update, $conexion); 
         if (! $resultado)
         {
            echo "No se pudo ejecutar la consulta ".$consulta_update;
         }                   
      }       
   }
   //---------------------------------------
   // verifica si hay uno nuevo e insertalo
   //---------------------------------------
   if (strlen($_POST['nuevo_paq'])>0)
   {
      // construye consulta de insercion
      $consulta_insertar = 'INSERT INTO paquetes_software(id_software,'.
         'nombre) VALUES('.$_POST['ids'].',"'.$_POST['nuevo_paq'].'")';
      // ejecuta consulta
      $resultado = mysql_query($consulta_insertar, $conexion); 
      if (! $resultado)
      {
         echo "No se pudo ejecutar la consulta ".$consulta_insertar;
      }  
   }
 }
 //--------------------------------------------------------------------
 // CHEQUEA LA IDENTIDAD DEL SOFTWARE
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, sin modificar valores
 if (strlen($_POST['ids']) > 0) // 
 {
   $id_software = $_POST['ids'];
 } 
 // para el caso de un enlace a la página de editar
 else
 {
   $id_software = $_GET['ids'];
 }
 
 // verifica que tras identificacion, tenemos un identificado valido
 if ((strlen($id_software)==0) || ($id_software == 0))
 {
        ERR_muestra_pagina_error("Software desconocido", "");
        exit;     
 }  

 //--------------------------------------------------------------------
 // OBTIENE LOS VALORES DE LOS PAQUETES DE SOFTWARE DE LA BASE DE DATOS
 //--------------------------------------------------------------------
 // crea la consulta del software
 $consulta_software = "SELECT id_paq_soft, nombre, version, ".
     " YEAR(fecha), MONTH(fecha), DAYOFMONTH(fecha), link_software ".
     " FROM paquetes_software WHERE id_software=".$id_software;
  
 // ejecuta la consulta para obtener datos
 $resultado = mysql_query($consulta_software, $conexion);
   
 if (! $resultado)
     {echo "Error al realizar la consulta ".$consulta_software;}

 //--------------------------------------------------------------------
 // RELLENA LOS VALORES DE LOS PAQUETES DE SOFTWARE
 //--------------------------------------------------------------------  
 $numero_paquetes = 1; 
 while ($registros = mysql_fetch_row($resultado))
 {  
   // rellena la fecha
   asigna_select_box (1, 31, $registros[5], $pagina, 
                    "main.form_paquetes.fila_paquete.fila_dia_inc");
   asigna_select_box (1, 12, $registros[4], $pagina, 
                    "main.form_paquetes.fila_paquete.fila_mes_inc");
   asigna_select_box (1990, 2010, $registros[3], $pagina, 
                    "main.form_paquetes.fila_paquete.fila_anyo_inc");
   
   // obtiene el nombre del fichero de link
   $inicio = strrpos($registros[6],'/');
   $nombre_fichero = substr($registros[6],$inicio+1);     
                     
   // rellena el resto de valores normales
   $lista_valores = array(
         'PQ_BORRAR' => 'pq_borrar_'.$numero_paquetes,
         'PQ_ID_SW' => 'pq_id_sw_'.$numero_paquetes,
         'ID_SW' => $registros[0],
         'PQ_NOMBRE' => 'pq_nombre_'.$numero_paquetes,
         'NOMBRE' => $registros[1],
         'PQ_VERSION' => 'pq_version_'.$numero_paquetes,
         'VERSION' => $registros[2],
         'PQ_DIA' => 'pq_dia_'.$numero_paquetes,
         'PQ_MES' => 'pq_mes_'.$numero_paquetes,
         'PQ_ANYO' => 'pq_anyo_'.$numero_paquetes,
         'PQ_FILE' => 'pq_file_'.$numero_paquetes,
         'PQ_LINK' => 'pq_link_'.$numero_paquetes,
         'LINK_FILE' => $registros[6],
         'FILE' => $nombre_fichero); 
  
  // imprime los valores en página
  $pagina->assign("LISTA",$lista_valores);
  $pagina->parse("main.form_paquetes.fila_paquete");
  
  $numero_paquetes ++;
 }
 // asigna el número de paquetes
 $pagina->assign("IDS",$id_software);
 $pagina->assign("NUM_PAQUETES",$numero_paquetes - 1);
 $pagina->parse("main.form_paquetes");
 //imprime resultado
 $pagina->parse("main");
 $pagina->out("main"); 

?>