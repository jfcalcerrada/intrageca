<?php

/**
 *
 */


// Carga la plantilla
$_content = new XTemplate('templates/' . $_lang . '/' . $_file . '.html');


// Asigna la variable miembro a la plantilla
$_content->assign('MIEMBRO', arrayUpper($miembro));


// Carga el submenu, puesto que sólo puede entrar el miembro o el admin
$isAdmin  = ($_SESSION['privilegios'] == ADMIN);

if ($isAdmin) {
    $_content->parse('content.submenu.borrar');
}
$_content->parse('content.submenu');


// Si existe el mensaje, lo asigna
if (!empty($mensaje)) {
    $_content->assign('MENSAJE', $mensaje);
}


// Si hay algún error, lo asigna
if (isset($error)) {
    $_content->assign('ERROR', arrayUpper($error));
}


// Añade los idiomas
// Imprime los idiomas disponibles
foreach ($_languages as $clave => $texto) {
    // Asigna la lista
    $idiomaCambiar = array(
        'clave' => $clave,
        'texto' => $texto);

    // Comprueba si es el idioma de los datos
    if ($clave == $idioma) {
        $idiomaCambiar['seleccionado'] = 'selected="selected"';
    }

    // Lo inserta en la página
    $_content->assign('IDIOMA', arrayUpper($idiomaCambiar));
    $_content->parse('content.formulario.idioma');
}


// Muestra la categorias
foreach ($_member['grupos'] as $clave => $texto) {
    // Asigna la categoria
    $categoria = array(
        'clave' => $clave,
        'texto' => $texto);

    // Comprueba si es el idioma de los datos
    if ($clave == $miembro['categoria']) {
        $categoria['seleccionado'] = 'selected="selected"';
    }

    // Lo inserta en la página
    $_content->assign('CATEGORIA', arrayUpper($categoria));
    $_content->parse('content.formulario.categoria');
}


// Si no es ADMIN no puede editar la fecha
if ($_SESSION['privilegios'] != ADMIN) {
    $_content->assign('FECHA_DISABLED', 'disabled="disabled"');
}

// Si es nuevo inhabilita marcarlo como activo
if ($miembro['id_miembro'] == 0) {
    $_content->assign('ACTIVO_DISABLED', 'disabled="disabled"');
}


// Si existe la foto la muestra
if (!empty($miembro['link_foto']) && file_exists($miembro['link_foto'])) {
    $_content->parse('content.formulario.foto');
}

// Si existe el curriculum lo muestra
if (!empty($miembro['link_curriculum']) 
    && file_exists($miembro['link_curriculum'])
) {
    $_content->parse('content.formulario.curriculum');
}


// Parsea el formulario
$_content->parse('content.formulario');

// Parse  todo el contenido
$_content->parse('content');


// Invluye el Layout
require_once 'includes/layout.php';


?>
