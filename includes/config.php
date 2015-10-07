<?php

/**
 * 
 */


// Los datos de la conexión a la db están en includes/pdo.php


// Listado de idiomas disponibles en la web
$_languages = array(
    'es'  => '',
    'us'  => '');

// Tiempo máximo por session, en segundos
define('TIEMPO_SESSION', 3600);

// Definicion de usuarios y privilegios
define('INVITADO', 0);
define('ADMIN', 1);
define('MIEMBRO', 2);

// Password por defecto para los nuevos usuarios
define('PASSWORD_DEFECTO', 'defecto6R#');


// Enlaces asociados a la parte superior de la página
$_titles['links'] = array(
    'grupo'         => 'presentacion.php',
    'departamento'  => 'http://www.tsc.uc3m.es',
    'universidad'   => 'http://www.uc3m.es');

// Directorio y tipos aceptados para las fotos
$_files['fotos'] = array(
    'dir'   => 'fotos',
    'size'  => 300000,
    'mime'  => 'image/jpeg image/jpg image/png');

// Directorio y tipos aceptados para los currículums
$_files['curriculums'] = array(
    'dir'   => 'docs/cv',
    'size'  => 1000000,
    'mime'  => 'application/pdf application/msword application/rtf');

// Directorio y tipos aceptados para llos
$_files['publicaciones'] = array(
    'dir'   => 'docs/public',
    'mime'  => 'application/pdf');

?>
