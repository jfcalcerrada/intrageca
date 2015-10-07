<?php

/**
 *
 */


global $_file;
global $_languages;
global $_titles;


// Obtenemos la url y el a�ade parte de la query ? o &
$url  = $_SERVER['REQUEST_URI'];
$url .= ( ! strpos($url, '?')) ? '?' : '&';


// Obtiene la informaci�n de la p�gina si no est� introducida previamente
if ( !isset($page['file'])) {
    $page['file'] = $_file;
}

if ( !isset($page['title'])) {
    $page['title'] = $_titles['titulos'][$_file];
}

if ( !isset($page['content'])) {
    $page['content'] = $_content->text('content');
}

if ( !isset($page['url'])) {
    $page['url'] = $url;
}


// Cargamos el XTemplate de la p�gina
$_page = new XTemplate('templates/' . $_lang . '/layout.html');

// Cargamos los datos de la p�gina y los t�tulos
$_page->assign('PAGE', arrayUpper($page));
$_page->assign('TITULOLINK', arrayUpper($_titles['links']));
$_page->assign('TITULONOMBRE', arrayUpper($_titles['nombres']));


// Si existe un archivo javascript lo incluimos
if (file_exists('javascript/' . $_file. '.js')) {
    $_page->parse('page.javascript');
}


// Incluimos los idiomas restantes
unset($_languages[$_lang]);

$_page->assign('LANGVALOR', reset($_languages));
$_page->assign('LANGCLAVE', key($_languages));
$_page->parse('page.idioma');


while ($valor = next($_languages)) {
    $_page->parse('page.idioma.siguiente');

    $_page->assign('LANGVALOR', $valor);
    $_page->assign('LANGCLAVE', key($_languages));
    $_page->parse('page.idioma');
}


// Rellena los datos de la sesiones si est� logueado
if (isset($_SESSION['id_miembro'])) {
    // Obtiene el rol del usuario
    $rol = ($_SESSION['privilegios'] == INVITADO)
        ? $_roles[MIEMBRO]
        : $_roles[INVITADO];

    $_page->assign('ROL', $rol);
    $_page->assign('MIEMBRO', arrayUpper($_SESSION));
    $_page->parse('page.miembro');


} else {
    // Si no esta logueado muestra el acceso
    $_page->parse('page.acceder');
}

// Parsea e imprime la p�gina
$_page->parse('page');
$_page->out('page');

?>
