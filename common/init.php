<?php

require_once __DIR__ . '/../includes/bootstrap.php';

// Incluimos los archivos necesarios en cada script
//require_once 'common/xtpl.php';
require_once 'common/func_aux.php';
require_once 'common/common_error.php';
require_once 'common/mysql.php';
require_once 'common/autenticacion.php';
require_once 'common/control_acceso.php';



$idioma = $_SESSION['lang'];
// Traducciones
require_once __DIR__ . '/../includes/languages/' . $_SESSION['lang'] . '.php';


// Realiza la conexion, necesario para autenticacion
$conexion = conectar_mysql($BASE_DATOS, $USER_BD, $PASS_BD);

// Obtiene el nombre de archivo
$archivo = $_file;


// Crea parser de la pagina, si no existe en el idioma, en español
if (file_exists("templates/$idioma/$archivo.html")) {
  $_content = new XTemplate("templates/$idioma/$archivo.html");
} else {
  $_content = new XTemplate("templates/es/$archivo.html");
}
