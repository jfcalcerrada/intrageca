<?php
// Inicializamos el archivo con el script
include('common/init.php');


/**
 * @name administracion.php
 *
 * @desc
 *
 * @access Administrador
 *
 */

// Controla el acceso
if ($_SESSION['privilegios'] != ADMIN)
  error($errors['privilegios'], 'No tiene privilegios para acceder',
    'miembros.php');

/* MUESTRA LA PAGINA */
// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>
