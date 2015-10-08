<?php

/**
 *
 * @param array $registro Contiene: idioma,
 *
 *
 * @return id_asignatura Identificador de la asignatura
 */

function docencia_insertar($registro) {

// Cargamos el array de errores
  GLOBAL $errors;


    /* PREPARAMOS LOS DATOS DE LA ASIGNATURA */
  // Transformam el array en variables
  foreach($registro as $clave => $valor)
    ${$clave} = $valor;


  // Comprueba si el enlace contiene http://
  if (strpos($link, 'http://') === false)
    $link = '';

  // Comprueba el valor de publico
  $publico = (isset($publico) && $publico == 1) ? 1 : 0;


    /* INSERTA/ACTUALIZA LOS DATOS NO DEPENDIENTES DEL IDIOMA */
  // Si el identificador es 0, crea la consulta de inserccion
  if ($id_asignatura == 0) {
  // Crea la consultade inserccion
    $consulta_asignatura =
        "INSERT INTO asignaturas(id_asignatura, link, publico) ".
        "VALUES ('', '$link', '$publico')";

  // Si el identificador es diferente de 0, crea la consulta de actualizacion
  } else {
  // Crea la consulta de actualizacion
    $consulta_asignatura =
        "UPDATE asignaturas ".
        "SET link = '$link', ".
        "publico = '$publico' ".
        "WHERE id_asignatura = $id_asignatura";
  }

  // Realiza la insercion/actualizacion del proyecto en la tabla principal
  mysql_query($consulta_asignatura)
      or error($errors['consulta'], "Error en la consulta: $consulta_asignatura");


    /* INSERTA/ACTUALIZA LOS DATOS DEPENDIENTES DEL IDIOMA */
  // Obtiene el id en caso de inserccion
  if ($id_asignatura == 0)
    $id_asignatura = mysql_insert_id();

  // Compruebasi existen los datos de la asignatura en el idioma
  $consulta_idioma =
      "SELECT id_asignatura, idioma ".
      "FROM asignatura_idiomas ".
      "WHERE id_asignatura = $id_asignatura ".
      "AND idioma = '$idioma'";

  $resultado_idioma = mysql_query($consulta_idioma)
      or error($errors['consulta'], "Error en la consulta: $consulta_idioma");

  // Si no hay entradas, crea la inserccion
  if (mysql_num_rows($resultado_idioma) == 0) {
  // Crea la consulta de inserccion
    $consulta_asignatura_idioma =
        "INSERT INTO asignatura_idiomas(id_asignatura, idioma, nombre) ".
        "VALUES ('$id_asignatura', '$idioma', '$nombre')";

  } else {
  // Crea la consulta de actualizacion
    $consulta_asignatura_idioma =
        "UPDATE asignatura_idiomas ".
        "SET nombre = '$nombre' ".
        "WHERE id_asignatura = $id_asignatura ".
        "AND idioma = '$idioma'";
  }


  // Realiza la insercion/actualizacion del proyecto en la tabla idioma
  mysql_query($consulta_asignatura_idioma)
      or error($errors['consulta'], "Error en la consulta: $consulta_asignatura_idioma");


    /* PROCEDE A BORRAR LOS MIEMBROS MARCADOS */
  // Recorre los miembros
  for ($i = 1; $i < $numero+1 ; $i++) {
    // Obtiene el id_miembro
    $id_miembro = $registro["id_miembro_$i"];

    // Si esta marcado para borrar
    if (isset($registro["borrar_$i"]) && $registro["borrar_$i"] == 1) {

      // Crea la consulta de borrado
      $consulta_borrar =
        "DELETE FROM asignatura_miembros ".
        "WHERE id_asignatura = $id_asignatura ".
          "AND id_miembro = $id_miembro";

      mysql_query($consulta_borrar)
        or error($errors['actualiza'], "Error en la consulta: $consulta_borrar");
    
    } else {
      $coordinador = (isset($_POST["coor_$i"]) && $_POST["coor_$i"] == 1) ? 1 : 0;

      $consulta_actualiza =
        "UPDATE asignatura_miembros ".
        "SET coordinador = $coordinador ".
        "WHERE id_miembro = $id_miembro";

      mysql_query($consulta_actualiza)
        or error($errors['actualiza'], "Error en la consulta: $consulta_actualiza");
    }
  }

    /* INSERTA AL NUEVO MIEMBRO */
  // Si esta introducido
  if (isset($nuevo_miembro) && strlen($nuevo_miembro) > 0) {

    // Si el nuevo miembro esta marcado como coordinador
    $coordinador = (isset($_POST['coor_nuevo']) && $_POST['coor_nuevo'] == 1) ? 1 : 0;

    // Añade al nuevo miembro
    $inserta_miembro =
        "INSERT INTO asignatura_miembros(id_asignatura, id_miembro, coordinador) ".
        "VALUES ('$id_asignatura', '$nuevo_miembro', '$coordinador')";

    mysql_query($inserta_miembro)
        or error($errors['actualiza'], "Error en la consulta: $inserta_miembro");
  }


  return $id_asignatura;
}

?>