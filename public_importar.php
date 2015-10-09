<?php

require_once 'common/init.php';

require_once 'common/common_pub.php';
require_once "bibtex/lectura_bibtex.php";
require_once "bibtex/inserta_BD_referencias.php";

//--------------------------------------------------------------------------
// public_importar.php
//
// Genera la página de importación de fichero Bibtex. En caso que la
// página haya sido llamada desde ella misma, importa el fichero que
// se le pasa por parámetro.
// 
// Los parámetros que necesita la página son:
//
//   fichero_bibtex: El fichero con los datos a importar
//--------------------------------------------------------------------------

  //variable de control de si se ha importado algun registro
  $registros_importados = FALSE;
  
  //crea log de insercion
  $log_insercion = array();
  
  // mira si hemos indicado un nuevo fichero de importación
  if (isset($_FILES['fichero_bibtex']) && strlen($_FILES['fichero_bibtex']['name']) > 0) {
       $registros_importados = TRUE;
       // extrae extension
       strtok($_FILES['fichero_bibtex']['name'],".");
       $extension = strtolower(strtok("."));

       // admite solo .bib
       if ($extension == 'bib') {
             // lee fichero bibtex
             $leido = lectura_fichero_bibtex($_FILES['fichero_bibtex']['tmp_name'], $bibliografia, $error_log);

             // insertalo en la base de datos si no hay error
             if ($leido) {
                 inserta_BD_referencias($bibliografia, $log_insercion);
             }
       }
  }
  else if (isset($_POST['texto_bib']) && strlen($_POST['texto_bib']) > 0) {
     $registros_importados = TRUE;
     // lee el texto bibtex
     $leido = lectura_texto_bibtex($_POST['texto_bib'], $bibliografia, $error_log);
     
     // si se ha leido algun registro, insertalo en la base de datos
     if ($leido) {
       inserta_BD_referencias($bibliografia, $log_insercion);
     }
  }

  // si no se importo bien el Bibtex, indicalo
  if ((isset($leido)) && ($leido == FALSE) && ($registros_importados)) {
     ERR_muestra_pagina_error($error_log, "");
     exit;          
  }

  
  if ($registros_importados) {
     // inicia contadores
     $contador_insercion = 0;
     $contador_actualizacion = 0;
     $contador_error = 0;
     $contador_avisos = 0;
        
     // inserta todos los registros de inserción
     foreach ($log_insercion as $entrada) {
        switch ($entrada[0]) {
           case 0:
              $_content->assign("ENTRADA",'<font color="#AA1111">'.$entrada[1].'</font>');
              $contador_error ++;
              break;
           case 1:
              $_content->assign("ENTRADA",'<font color="#DC8010">'.$entrada[1].'</font>');
              $contador_actualizacion ++;
              break;
           case 2:
              $_content->assign("ENTRADA",'<font color="#11AA11">'.$entrada[1].'</font>');
              $contador_insercion ++;
              break;
           case 3:
              $_content->assign("ENTRADA",'<font color="#AA1111">'.$entrada[1].'</font>');  
              $contador_avisos ++;               
          }
          
        // insertala en página
        $_content->parse("main.log.fila_log");
     }
     // Imprime estadisticas
     $_content->assign("NUM_ACTUALIZADOS",$contador_actualizacion);
     $_content->assign("NUM_INSERTADOS",$contador_insercion);
     $_content->assign("NUM_ERROR",$contador_error);
     $_content->assign("NUM_AVISOS",$contador_avisos);
     $_content->parse("main.log");
  }

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
