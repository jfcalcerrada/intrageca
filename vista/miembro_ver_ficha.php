<?php

/**
 *
 */

 
// Carga la plantilla
$_content = new XTemplate('templates/' . $_lang . '/' . $_file . '.html');


// Asigna la variable miembro a la plantilla
$_content->assign('MIEMBRO', arrayUpper($miembro));


// Si es el miembro o si es el ADMIN, carga el submenu
$isAdmin  = ($_SESSION['privilegios'] == ADMIN);

if ($_SESSION['id_miembro'] == $id_miembro || $isAdmin) {
    // Si es Administrador, muestra el menu de borrar
    if ($isAdmin) {
        $_content->parse('content.submenu.borrar');
    }
    $_content->parse('content.submenu');
}


// Si existe la foto, muestra la foro
if (file_exists($miembro['link_foto'])) {
    $_content->parse('content.datos.foto');
}

// Si no es invitado, muestra la fecha de incorporacion
if ($_SESSION['privilegios'] != INVITADO) {
    $_content->parse('content.datos.incorporacion');
}


// Parse los datos
$_content->parse('content.datos');


// Si tiene asignaturas asociadas, muestra el link
if ($docencia) {
    $_content->parse('content.docencia');
}


// Si tiene publicaciones
if (!empty($publicaciones)) {
    $_content->assign('PUBLICACIONES', $publicaciones);
    $_content->parse('content.publicaciones');
}


// Si tiene curriculum, muestra el link
if ($hasCurriculum) {
    $_content->parse('content.curriculum');
}


// Parse todo el contenido
$_content->parse('content');


// Incluye el Layout
require_once 'includes/layout.php';


?>
