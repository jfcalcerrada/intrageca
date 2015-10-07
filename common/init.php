<?php
// Incluimos los archivos necesarios en cada script
require('config.php');
require('common/xtpl.php');
require('common/func_aux.php');
require('common/common_error.php');
require('common/mysql.php');
require('common/autenticacion.php');
require('common/control_acceso.php');


// Crea la sesion o coge la existente
session_start();

// Controla el tiempo de session
//control_tiempo_session();

// Si no hay usuario aun, es un invitado
if (!isset($_SESSION['privilegios'])) {
// Carga los privilegios a 0
  $_SESSION['privilegios'] = 0;
  // Carga el idioma en la session
  $_SESSION['idioma'] = 'es';
}


// Si se cambia el idioma, lo cargamos en session
if (isset($_GET['lang']) && strlen($_GET['lang']) > 0)
  $_SESSION['idioma'] = $_GET['lang'];

// Determinamos el idioma
$idioma = determinar_idioma($_SESSION['idioma'], $gen_idiomas_disp);

// Incluye las definiciones dependedientes del idioma
include("common/def_$idioma.php");

// Realiza la conexion, necesario para autenticacion
$conexion = conectar_mysql($BASE_DATOS, $USER_BD, $PASS_BD);

// Obtiene el nombre de archivo
$archivo = basename($_SERVER['SCRIPT_NAME'], '.php');

// Crea parser de la pagina, si no existe en el idioma, en español
if (file_exists("templates/$idioma/$archivo.html")) {
  $contenido = new XTemplate("templates/$idioma/$archivo.html");
} else {
  $contenido = new XTemplate("templates/es/$archivo.html");
}


// Autenticamos al usuario si se solicita
if (isset($_GET['login']) && $_GET['login'] == 1)
  autenticar_usuario();

// Cambiar rol usuario/visitante, si se solicita
if (isset($_GET['rol']) && $_GET['rol'] == 1) {

  // Si no esta invitado, le ponemos como invitado y salvamos privilegios
  if ($_SESSION['privilegios'] != 0) {
    // Guardamos los privilegios
    $_SESSION['privilegios_old'] = $_SESSION['privilegios'];
    // Lo ponemos como invitado
    $_SESSION['privilegios'] = 0;

  // Si quiere volver, restauramos sus privilegios
  } else {
    $_SESSION['privilegios'] = $_SESSION['privilegios_old'];
  }
}


?>