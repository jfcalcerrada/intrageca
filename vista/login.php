<?php

/**
 *
 */


// Carga la plantilla
$_content = new XTemplate('templates/' . $_lang . '/' . $_file . '.html');


// Si no esta logueado
if (!isset($_SESSION['id_miembro'])) {
    $_content->parse('content.formulario');
    

} else {
    // Si está registrado, puestra el mensaje

    // Muestra un mensaje u otro dependiendo de si se ha rellenado el formulario
    if (isset($_POST['usuario'])) {
        $_content->parse('content.accedido');
    } else {
        $_content->parse('content.registrado');
    }
}


// Parse todo el contenido
$_content->parse('content');


// Incluye el Layout
require_once 'includes/layout.php';


?>
