<?php

/**
 * Muestra el menu de los Miembros
 * @param   XTemplate   contenido   Plantilla de contenido XTemplate
 * @param   integer   id_miembro  Identificador del miembro
 */
function menu_miembros($contenido, $id_miembro)
{

  // Si no hay nuevo usuario
  if ($id_miembro != 0 && ($id_miembro == $_SESSION['id_miembro']
      || $_SESSION['privilegios'] == ADMIN)) {
    // Carga el identificador de miembro
    $contenido->assign('ID_MIEMBRO', $id_miembro);

    // Si es ADMIN muestra el boton de borrar
    if ($_SESSION['privilegios'] == ADMIN)
      $contenido->parse('content.menu.borrar');

    // Muestra el menu
    $contenido->parse('content.menu');
  }

  return $contenido;
}

/**
 * Control de acceso de Miembros a su propia página
 * @param   integer   id_miembro  Identificador del miembro
 */
function acceso_miembro($id_miembro)
{

  // Cargamos los errores
  global $errors;

  // Si no es el administrador o el miembro en si deniega el acceso
  if (!($_SESSION['id_miembro'] == $id_miembro
      || $_SESSION['privilegios'] == ADMIN))
    error($errors['privilegios'], 'No tiene privilegios para acceder',
      'miembros.php');
}

?>