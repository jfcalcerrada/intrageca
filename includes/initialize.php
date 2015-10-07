<?php

/**
 *
 */

 
// Incluye el fichero de configuracion
require_once 'includes/config.php';

// Incluye los archivos necesarios, error y XTemplate
require_once 'includes/access.php';
require_once 'includes/api.php';
require_once 'includes/error.php';
require_once 'includes/xtemplate.php';

// Carga la sesion y controla que no se haya excedido en tiempo
session_start();


// Realiza las funciones asociadas al idioma
require_once 'includes/language.php';

// Carla la clase de la base de datos
require_once 'includes/db.php';

// Realiza las funciones de la Session
require_once 'includes/session.php';


// Obtiene el idioma
$_lang = $_SESSION['lang'];

// Obtiene el nombre de archivo
$_file = basename($_SERVER['SCRIPT_NAME'], '.php');

?>
