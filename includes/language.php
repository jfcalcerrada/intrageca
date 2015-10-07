<?php

/**
 * 
 */

// Si no hay seleccionado idioma, obtiene el primero, que es el de por defecto
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = key($_languages);
}


// Si hay idioma en la url, comprueba que existe el idioma y lo cambia
if (isset($_GET['lang']) && !empty($_GET['lang'])) {
    $_SESSION['lang'] = validateLanguage($_GET['lang']);
}


require_once 'includes/languages/' . $_SESSION['lang'] . '.php';


/**
 * Función que valida el camibo de idioma, si el idioma introducido existe lo
 * guarda, si no existe mantiene el idioma actual
 *
 * @param string $newLang Clave del array del idioma a cambiar
 *
 * @return string Devuelve el idioma
 */
function validateLanguage($newLang)
{
    global $_languages;

    return (isset($_languages[$newLang])) ? $newLang : $_SESSION['lang'];
}


?>
