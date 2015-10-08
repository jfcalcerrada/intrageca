<?php

// Obtiene la información de la página si no está introducida previamente
if (!isset($page['file'])) {
    $page['file'] = $_file;
}

if (!isset($page['title']) && isset($_titles['titulos'][$_file])) {
    $page['title'] = $_titles['titulos'][$_file];
}

if (!isset($page['content'])) {
    $page['content'] = $_content->text('content');
}



// Cargamos el XTemplate de la página
$_page = new XTemplate('templates/' . $_lang . '/layout.html');

// Cargamos los datos de la página y los títulos
$_page->assign('PAGE', arrayUpper($page));
$_page->assign('TITULOLINK', arrayUpper($_titles['links']));
$_page->assign('TITULONOMBRE', arrayUpper($_titles['nombres']));


// Si existe un archivo javascript lo incluimos
if (file_exists('javascript/' . $_file. '.js')) {
    $_page->parse('page.javascript');
}


// Incluimos los idiomas restantes
unset($_languages[$_lang]);


$i = 0;
foreach ($_languages as $key => $value) {
    // Except for the first language
    if ($i++) {
        $_page->parse('page.idioma.siguiente');
    }

    $_page->assign('LANG', array(
        'VALUE' => $value,
        'URL'   => url(null, array('lang' => $key))
    ));
    $_page->parse('page.idioma');
}


// Rellena los datos de la sesiones si está logueado
if (isset($_SESSION['id_miembro'])) {
    // Obtiene el rol del usuario
    $_page->assign('ROL', array(
        'NAME' => ($_SESSION['privilegios'] === INVITADO) ? $_roles[MIEMBRO] : $_roles[INVITADO],
        'URL'  => url(null, array('rol' => 1)),
    ));

    $_page->assign('MIEMBRO', array(
        'ID'     => $_SESSION['id_miembro'],
        'NOMBRE' => $_SESSION['nombre'],
        'URL'    => url('miembro_ver_ficha.php', array('id_miembro' => $_SESSION['id_miembro'])),
    ));
    $_page->parse('page.miembro');

    if ($_SESSION['privilegios'] === ADMIN) {
        $_page->parse('main.administracion');
    }

} else {
    // Si no esta logueado muestra el acceso
    $_page->parse('page.acceder');
}

// Parsea e imprime la página
$_page->parse('page');
$_page->out('page');
