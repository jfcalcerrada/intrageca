<?php

require_once __DIR__ . '/common/init.php';

require_once "common/common_mbr.php";

// Si existen parámetros
if(count($_GET) > 0) {

  // Creamos la cadena de búsqueda y redirigimos
  $cadena_busqueda = "?logica=OR";

  $i = 1;
  foreach($_GET as $key => $value) {
    if (strpos($key, "miembro") !== false) {
      $consulta_bibtex =
        "SELECT texto_bibtex ".
        "FROM miembro_bibtex ".
        "WHERE id_miembro = '$value'";

      if (!($resultado_bibtex = mysql_query($consulta_bibtex))) {
        ERR_muestra_pagina_error("Error en consulta: $consulta_bibtex");
      } else {
      // Cargamos todos los posibles autores
        while ($bibtex = mysql_fetch_array($resultado_bibtex)) {
          $cadena_busqueda .= "&campo{$i}=author&valor{$i}={$bibtex['texto_bibtex']}";
          $i++;
        }

      }
    } elseif (strpos($key, "proyecto") !== false) {
      $cadena_busqueda .= "&campo{$i}=OPTproyecto&valor{$i}={$value}";
      $i++;
    }
  }

  // Añadimos las fechas
  if(isset($_GET['desde']) && strlen($_GET['desde']) > 0)
    $cadena_busqueda .= "&desde={$_GET['desde']}";

  if(isset($_GET['hasta']) && strlen($_GET['hasta']) > 0)
    $cadena_busqueda .= "&hasta={$_GET['hasta']}";

  //echo $cadena_busqueda;
  header("Location: public_busqueda.php$cadena_busqueda");

} else {
// Si no los hay, mostramos la página de la memoria de grupo


// Mostramos los miembros de los que queremos la bibliografia
  $consulta_miembros =
      "SELECT id_miembro, nombre, apellidos ".
      "FROM miembros ".
      "ORDER BY apellidos ASC";

  if(!($resultado_miembros = mysql_query($consulta_miembros))) {
    ERR_muestra_pagina_error("Error en consulta: $consulta_miembros");
  } else {
  // Los mostramos
    for($i = 0; $miembro = mysql_fetch_array($resultado_miembros); $i++) {
      $_content->assign("MIEMBRO", array_change_key_case($miembro, CASE_UPPER));
      $_content->assign("NUMERO", $i);
      $_content->parse("content.miembro");
    }

  }

  $consulta_proyectos =
      "SELECT id_proyecto,id_pr_bibtex ".
      "FROM proyectos ";

  if(!($resultado_proyectos = mysql_query($consulta_proyectos))) {
    ERR_muestra_pagina_error("Error en consulta: $consulta_proyectos");
  } else {
  // Los mostramos
    for($i = 0; $proyecto = mysql_fetch_array($resultado_proyectos); $i++) {
      $_content->assign("PROYECTO", $proyecto);
      $_content->assign("NUMERO", $i);
      $_content->parse("content.proyecto");
    }

  }


// Mostramos las fechas de inicio y fin

}

// Cierra la conexion con mysql
mysql_close($conexion);

$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';