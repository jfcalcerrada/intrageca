<?php
// Cargamos los includes de la cabecera
require_once 'includes/initialize.php';

/**
 * P�gina que se encarga de terminar la sesi�n y redirigir a la p�gina de
 * bienvenida
 *
 */

// Termina la session
session_unset();


// Redirecciona a la p�gina de inicio
header('Location: index.html');

?>