<?php
// Inicializamos el archivo con el script
include('common/init.php');



/**
 * @name  miembro_ver_ficha.php
 *
 * @desc  Pagina que genera la ficha del miembro solicidado. Para ello necesita
 * que sea introducido por parámetro el Identificador de Miembro.
 * @access  Público   para los miembros "Activos".
 * @access  Privado   para los miembros "No activos".
 * @param   idm   Identificador del Miembro
 */


// Extrae únicamente lo parámetrosque son aceptados en esta página
extract(array_intersect_key($_GET, array('idm'=>'')));

// Obtiene el id_miembro y verifica si es correcto
$id_miembro = validar_id($idm);


/* CONSULTA LOS DATOS DEL MIEMBRO */
// Si es un invitado solo puede ver los miembros activos, es decir 1
$activo = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

// Creamos la consulta, a partir del identificador y el idioma
$consulta_miembro =
  "SELECT nombre, curriculum, link_curriculum ".
  "FROM miembros LEFT JOIN miembro_idiomas ".
    "ON miembros.id_miembro = miembro_idiomas.id_miembro ".
  "WHERE miembros.id_miembro = '$id_miembro' ".
    "AND idioma = '$idioma' ".
    "AND activo >= $activo ";

// Realizamos la consulta y comprobamos que no da errores
$resultado_miembro = mysql_query($consulta_miembro)
  or error($errors['consulta'], "Error en la consulta: $consulta_miembro");

// Comprobamos si el miembro existe, es decir, produce resultado
if (mysql_num_rows($resultado_miembro) == 0)
  error($errors['miembro'],
    "El miembro no existe o invitado, identificador: $id_miembro");

// Obtiene los datos del miembro
$miembro = mysql_fetch_array($resultado_miembro);


// Comprueba si existe el archivo del curriculum del miembro
if (file_exists($miembro['link_curriculum'])) {
  // Cremos el link del curriculum y lo parseamos
  $contenido->assign('LINK_CURRICULUM', $miembro['link_curriculum']);
  $contenido->parse('content.curriculum');
}


// Formatea el texto del curriculum para hacerlo HTML
$miembro['curriculum'] = htmlentities(stripslashes($miembro['curriculum']));
$miembro['curriculum'] =
  str_replace(array("\n", "\r"), array('<br />', ''),$miembro['curriculum']);

// Asignamos la información de miembro
$contenido->assign('MIEMBRO', array_upper($miembro));

// Cierra la conexion con mysql
mysql_close($conexion);

// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>