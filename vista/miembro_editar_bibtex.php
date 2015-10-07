<?php

/**
 *
 */


// Carga la plantilla
$_content = new XTemplate('templates/' . $_lang . '/' . $_file . '.html');


// Asigna la variable miembro a la plantilla
$_content->assign('MIEMBRO', arrayUpper(array('id_miembro' => $id_miembro)));


// Carga el submenu, puesto que sólo puede entrar el miembro o el admin
$isAdmin  = ($_SESSION['privilegios'] == ADMIN);

if ($isAdmin) {
    $_content->parse('content.submenu.borrar');
}
$_content->parse('content.submenu');


// Carga el mensaje si ha habido algún cambio
if (!empty($mensaje)) {
    $_content->assign('MENSAJE', $mensaje);
}


// Muestra las referencias
$count = count($bibtex);
// Recorre el array de referencias
for ($i = 0; $i < $count; ++$i) {
    // Le añade el índice
    $bibtex[$i]['indice'] = $i;

    // Asigna el array y lo muestra
    $_content->assign('BIBTEX', arrayUpper($bibtex[$i]));
    $_content->parse('content.bibtex.referencia');
}


// Añade el número de campos
$_content->assign('NUMERO', $count);


// Parsea el formulario
$_content->parse('content.bibtex');

// Parse  todo el contenido
$_content->parse('content');


// Invluye el Layout
require_once 'includes/layout.php';


?>
