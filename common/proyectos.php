<?php

/**
 * Muestra el menu de los Proyectos
 * @param contenido Plantilla de contenido XTemplate
 * @param id_miembro Identificador del miembro
 */
function menu_proyectos($contenido, $id_proyecto) {

  if ($id_proyecto != 0 && $_SESSION['privilegios'] != INVITADO) {
    // Consulta para comprobar si el miembro es responsable
    $consulta_responsable =
      "SELECT id_miembro ".
      "FROM proyecto_miembros ".
      "WHERE id_proyecto = $id_proyecto ".
        "AND id_miembro = {$_SESSION['id_miembro']} ".
        "AND responsable = 1";

    // Realizamos la consulta y comprobamos que no da errores
    $resultado_responsable = mysql_query($consulta_responsable)
      or error($errors['consulta'], "Error en la consulta: $consulta_responsable");

    // Si efectivamente es responsable o es el admin, mostramos el menu superior
    if (mysql_num_rows($resultado_responsable) > 0 || $_SESSION['privilegios'] == ADMIN) {

      // Carga el identificador de miembro
      $contenido->assign('ID_PROYECTO', $id_proyecto);

      // Muestra el menu
      $contenido->parse('content.menu');
    }
  }

  return $contenido;
}


/**
 * Control de acceso de Miembros
 */
function acceso_proyecto($id_proyecto) {
  // Cargamos los errores
  GLOBAL $errors;

  // Consulta para comprobar si el miembro es responsable
  $consulta_responsable =
    "SELECT id_miembro ".
    "FROM proyecto_miembros ".
    "WHERE id_proyecto = $id_proyecto ".
      "AND id_miembro = {$_SESSION['id_miembro']} ".
      "AND responsable = 1";

  // Realizamos la consulta y comprobamos que no da errores
  $resultado_responsable = mysql_query($consulta_responsable)
      or error($errors['consulta'], "Error en la consulta: $consulta_responsable");

  // Si no es el administrador o el miembro en si deniega el acceso
  if ( !(mysql_num_rows($resultado_responsable) > 0 || $_SESSION['privilegios'] == ADMIN))
    error($errors['privilegios'], 'No tiene privilegios para acceder', 'miembros.php');

}

?>