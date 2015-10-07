<?php
// Cargamos los includes de la cabecera
require_once 'includes/initialize.php';

/**
 * Página que se encarga de terminar la sesión y redirigir a la página de
 * bienvenida
 *
 */

// Termina la session
session_unset();


// Redirecciona a la página de inicio
header('Location: index.html');

?>