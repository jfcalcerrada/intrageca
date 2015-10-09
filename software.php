<?php

require_once 'common/init.php';

// Autenticamos al usuario
autenticar_usuario();

//--------------------------------------------------------------------------
// software.php
//
// Genera la página principal de software y los exporta al template
// software.html. Se genera una lista con el enlace a su página de
// información.
//--------------------------------------------------------------------------


 //--------------------------------------------------------------------
 // CONSULTA DE PROYECTOS
 //--------------------------------------------------------------------   
  // definicion de consultas de la base de datos
  $consulta_software = 'SELECT software.id_software, titulo, descrip_corta, '.
        ' publico FROM software_idiomas LEFT JOIN software '.
        ' ON software_idiomas.id_software=software.id_software '.
        ' WHERE idioma="'.$idioma.'" ORDER BY publico DESC, titulo ASC';
  
  // realiza consulta para ver campos distintos
  $resultado = mysql_query($consulta_software, $conexion);
  $ultimo_soft = 1;
    
  // imprime para cada miembro del grupo una entrada
  while ($software = mysql_fetch_row($resultado))
  {
    if (($ultimo_soft != $software[3]) && ($software[3] == 0))
    {
      // imprime todos los publicos
      $_content->parse("content.tabla_software");
      // imprime cabecera de no activos
      $_content->parse("content.tabla_software.cab_desactiva");
      // actualiza el valor de proyecto
      $ultimo_soft = $software[3];
    }
    // asigna valores de miembro
    $lista_valores = array ( 
                       'IDS' => $software[0],
                       'TITULO' => $software[1],
                       'DESC_CORTA' => $software[2]);
    // imprimelos en página
    $_content->assign("LISTA1",$lista_valores);
    $_content->parse("content.tabla_software.fila");
  }
  
  // cierra tabla
  $_content->parse("content.tabla_software");

  // cierra descriptor
  mysql_close($conexion);

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
