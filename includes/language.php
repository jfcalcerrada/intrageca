<?php

// Si no hay seleccionado idioma, obtiene el primero, que es el de por defecto
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = key($_languages);
}


// Si hay idioma en la url, comprueba que existe el idioma y lo cambia
if (isset($_GET['lang']) && !empty($_GET['lang'])) {
    $_SESSION['lang'] = (isset($_languages[$_GET['lang']])) ? $_GET['lang'] : $_SESSION['lang'];
}

require_once 'includes/languages/' . $_SESSION['lang'] . '.php';

// TODO function to validate languages (here and in forms!)