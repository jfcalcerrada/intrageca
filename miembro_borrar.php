<?php

require_once 'common/init.php';

include('common/miembros.php');


/**
 * @name  borrar_miembro.php
 *
 * @desc  Borra todas las entradas de la base de datos relacionadas con el
 * miembro solicitado. Para el borrado se requiere de confirmacion.
 * @access  Administrador
 * @param   idm   Identificador del Miembro
 */


// Extrae únicamente lo parámetrosque son aceptados en esta página
extract(array_intersect_key(array_merge($_GET, $_POST),
    array('idm' => '', 'id_miembro' => '')));

// Obtiene el id_miembro y verifica si es correcto
$id_miembro = (isset($id_miembro)) ? validar_id($id_miembro) : validar_id($idm);


// Controla el acceso a la pagina
if ($_SESSION['privilegios'] != ADMIN)
  error($errors['privilegios'], 'No tiene privilegios para acceder', 'miembros.php');


/* VERIFICA SI TIENE QUE BORRAR EL MIEMBRO TRAS LA AUTOLLAMADA */
$borrar = (isset($_POST['borrar']) && $_POST['borrar'] == 1) ? true : false;


// Si aun no ha confirmado, muestra confirmacion
if ($borrar == false) {
  /* MUESTRA LA CONFIRMACION */
  // Obtenemos el nombre del miembro para mostrarlo
  $consulta_miembro =
    "SELECT id_miembro, nombre ".
    "FROM miembros ".
    "WHERE id_miembro = '$id_miembro'";

  // Realizamos la consulta y comprobamos que no da errores
  $resultado_miembro = mysql_query($consulta_miembro)
    or error($errors['consulta'], "Error en la consulta: $consulta_miembro");

  // Chequea si existe dicho miembro, deberia existir puesto que ha accedido
  if (mysql_num_rows($resultado_miembro) == 0)
    error($errors['miembro'], 'Problema en el archivo miembro borrar');

  // Obtiene los datos del miembro
  $miembro = mysql_fetch_array($resultado_miembro);

  // Asigna los valores y lo parsea
  $_content->assign('MIEMBRO', array_upper($miembro));
  $_content->parse('content.confirmar');

  // Muestra el submenú, y si es el administrador el botón de borrar
  $_content = menu_miembros($_content, $id_miembro);


// Si el usuario ha confirmado, realiza el borrado
} else {
  
    /* BORRAR EL MIEMBRO */
    // Crea las sentencias de borrado
    $sentencia_borrado = array(
      "DELETE FROM miembros WHERE id_miembro = '$id_miembro'",
      "DELETE FROM miembro_idiomas WHERE id_miembro = '$id_miembro'",
      "DELETE FROM miembro_bibtex WHERE id_miembro = '$id_miembro'",
      "DELETE FROM asignatura_miembros WHERE id_miembro = '$id_miembro'",
      "DELETE FROM proyecto_miembros WHERE id_miembro = '$id_miembro'",
      "DELETE FROM miembro_autentica WHERE id_miembro = '$id_miembro'");

    // Ejecuta el borrado
    foreach ($sentencia_borrado as $borrado_miembro)
      mysql_query($borrado_miembro)
        or error($errors['consulta'], "Error en la consulta: $borrado_miembro");

    // Muestra el mensaje de borrado
    $_content->parse('content.borrado');
}

// Cierra la conexion
mysql_close($conexion);


// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
