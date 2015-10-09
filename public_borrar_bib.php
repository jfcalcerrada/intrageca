<?php

require_once 'common/init.php';

require_once 'common/common_pub.php';

//--------------------------------------------------------------------------
// public_borrar_bib.php
//
// Borra de la base de datos todas las referencias bibliográficas.
// Almacena en el directorio de backup una copia de lo almacenado en
// la base de datos en formato .bib
// 
// Los parámetros que necesita la página son:
//
//   borrar: flag para indicar que se lleve a cabo el borrado o simplemente
//           muestre la página de borrado
//--------------------------------------------------------------------------

if ($_SESSION['privilegios'] != ADMIN) {
    ERR_muestra_pagina_error("No tiene privilegios");
}


  // definicion de Commons usados
  global $public_dir_backup;

  // chequea parametro de entrada
  if (!isset($_GET['borrar']) || $_GET['borrar'] != 1) {
      // Parsea el contenido
      $_content->parse("content");
      require_once __DIR__ . '/includes/layout.php';
      exit();
  }


 // construye nombre de fichero
 $nombre_fichero_bck = $public_dir_backup."backup_bib_".date("YmdHis").".bib";
 
 // crea el fichero
 $desc = fopen ($nombre_fichero_bck,'w');
 
 // si no puede abrir fichero, da mensaje de error
 if (!$desc) {
       ERR_muestra_pagina_error("No se pudo crear fichero de backup".
          " Fichero: ".$nombre_fichero_bck, "");
       exit;         
 } 

 //--------------------------------------------------------------------
 // LEE LOS SINONIMOS DE LA TABLA
 //-------------------------------------------------------------------- 
 $consulta_sinonimos = 'SELECT cadena, valor FROM ref_cadenas';
 $resultado = mysql_query($consulta_sinonimos, $conexion);
 
 while ($fila = mysql_fetch_row($resultado)) {
     fwrite($desc,"\n@string{".$fila[0]." = ".$fila[1]."}");
 }
 
 mysql_free_result($resultado);

 fwrite($desc,"\n\n");

//--------------------------------------------------------------
//  REALIZA BACKUP DE REFERENCIAS
//--------------------------------------------------------------
 // realiza consulta de referencias
 $consulta_ref = 'SELECT id_referencia, id_ref_bibtex, tipo,'.
  ' idioma, estado, link_referencia, tipo_link FROM referencias';
 
 // ejecuta consulta
 $resultado=mysql_query($consulta_ref, $conexion); 

 // chequea si ha habido error
 if (!$resultado) {
       ERR_muestra_pagina_error("No se pudo consultar tabla.".
          " Error de consulta: ".$consulta_ref, "");
       exit;         
 }
 
 // para cada una de ellas
 while ($referencia = mysql_fetch_row($resultado)) {
   // escribe cabecera en el fichero
   fwrite($desc,'@'.$referencia[2].'{'.$referencia[1]);
 
   // realiza consulta para obtener campos de la relacion directa
   // del registro
   $consulta_campos = 'SELECT campo, valor FROM ref_relacion '.
    'LEFT JOIN ref_campos ON ref_relacion.id_campos=ref_campos.id_campo_ref'.
    ' WHERE referencia_cruzada=0 AND id_ref='.$referencia[0];

    // ejecuta consulta
    $resultado2=mysql_query($consulta_campos, $conexion); 

    // chequea si ha habido error
   if (!$resultado2) {
       ERR_muestra_pagina_error("No se pudo consultar tabla.".
          " Error de consulta: ".$consulta_campos, "");
       exit;         
   }

   // obtiene todos los campos y escribelos en el fichero
   while ($ref_campos = mysql_fetch_row($resultado2)) {
     fwrite($desc,",\n  ".$ref_campos[0]." = ".$ref_campos[1]);
   }
   // imprime el campo OPTidioma, OPTestado y OPTenlace
   fwrite($desc,",\n  OPTidioma = ".$referencia[3]);
   fwrite($desc,",\n  OPTestado = ".$referencia[4]);
   if ($referencia[6] != 'N')
   {
        fwrite($desc,",\n  OPTenlace = ".$referencia[5]);
   }   
   // escribe una llave y dos saltos de linea para cerrar registro
   fwrite($desc,"\n}\n\n");
   
 }
 
 // cierra fichero
 fclose($desc);
  
//--------------------------------------------------------------
//  EJECUTA CONSULTAS DE BORRADO
//--------------------------------------------------------------

  // define consultas de borrado
  $consultas_borrar = array(
     "DELETE FROM ref_cadenas ", 
     "DELETE FROM ref_campos ",
     "DELETE FROM ref_relacion",
     "DELETE FROM referencias " );
  
  // ejecuta todas las consultas
  foreach ($consultas_borrar as $consulta) {
     // ejecuta consulta
     $resultado=mysql_query($consulta, $conexion);
     
     // chequea si ha habido error
     if (!$resultado) {
       ERR_muestra_pagina_error("No se pudo borrar tabla.".
          " Error de consulta: ".$consulta, "");
       exit;         
     }
  }
  // cierra descriptor
  mysql_close($conexion);  
  
  // muestra mensaje de todo OK
  ERR_muestra_pagina_mensaje("Se han eliminado todas las referencias de las tablas.", "");   
