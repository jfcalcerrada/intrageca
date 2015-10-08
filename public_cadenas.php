<?php

require_once 'common/init.php';

require_once 'common/common_pub.php';
require_once 'public_insertar.php';

//--------------------------------------------------------------------------
// public_cadenas.php
//
// Genera la página de edición de cadenas del Bibtex
// 
// Los parámetros que necesita la página son:
//
//  modificado : Indica si se ha modificado el formulario y debe actualizarse
//  numero_cadenas : El número de cadenas que hay en la página
//  i_x : El identificador de la cadena x
//  t_x : El texto de la cadena x
//  b_x : La orden de borrar la cadena x  
//  nuevo_campo : Nuevo sinonimo a introducir
//  nuevo_texto : Valor del nuevo sinonimo
//--------------------------------------------------------------------------

  // definicion de Config usados
  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;
  
  // crea parser de la página
  $pagina=new XTemplate ("templates/es/public_cadenas.html");
  
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
 // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTROS TRAS AUTOLLAMADA
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, modificando valores
 if ($_POST['modificado'] == 1) 
 {
   // recorremos lista de cadenas y actualizamos o borramos
   // segun se haya marcado el campo borrar
   for ($i=1; $i<=$_POST['numero_cadenas']; $i++)
   {
      if ($_POST["b_$i"] == 1)  // orden de borrado
      {
        $consulta_mod = 'DELETE FROM ref_cadenas WHERE cadena="'.
         $_POST["i_$i"].'"'; 
      }
      else  // orden de actualizacion
      {
         $consulta_mod = 'UPDATE ref_cadenas SET valor="'.$_POST["t_$i"].
          '" WHERE cadena="'.$_POST["i_$i"].'"';
      }
      // ejecuta consulta
      $resultado = mysql_query($consulta_mod, $conexion);
      if (!$resultado)
      {
         echo "No se pudo ejecutar la consulta ".$consulta_mod;
      }
   }
   
   // verifica si hay un nuevo campo
   if (strlen($_POST['nuevo_campo'])>0)
   {
      $consulta_mod = 'INSERT INTO ref_cadenas(cadena,valor) VALUES("'.
       $_POST['nuevo_campo'].'","'.$_POST['nuevo_texto'].'")';

      // ejecuta consulta
      $resultado = mysql_query($consulta_mod, $conexion);
      if (!$resultado)
      {
         ERR_muestra_pagina_error("Sinónimo ya definido","");
         return;
      }
   }
 }


 //--------------------------------------------------------------------
 // OBTIENE LOS VALORES DE CADENAS DE LA BASE DE DATOS
 // E INSERTALOS EN LA PÁGINA
 //--------------------------------------------------------------------
 $numero_cadenas=0;
 
 $consulta_cadenas = 'SELECT cadena, valor FROM ref_cadenas ORDER BY cadena ASC';
 
 $resultado = mysql_query($consulta_cadenas, $conexion);
 
 while ($fila = mysql_fetch_row($resultado))
 {
   $numero_cadenas = $numero_cadenas + 1;
   
   $lista_valores = array (
       'SINONIMO' => $fila[0],
       'ID_SIN'   => "i_$numero_cadenas",
       'T_SIN'    => "t_$numero_cadenas",
       'B_SIN'    => "b_$numero_cadenas",
       'TEXTO_SIN'=> $fila[1]);
   
   $pagina->assign('LISTA',$lista_valores);
   $pagina->parse("main.fila_cadena");
 }
 
 // cierra descriptor
 mysql_close($conexion);
 
 //imprime resultado
 $pagina->assign("NUM_CADENAS",$numero_cadenas); 
 $pagina->parse("main");
 $pagina->out("main"); 

?>