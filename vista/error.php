<?php

/**
 *
 */


// Carga la plantilla
$_content = new XTemplate('templates/' . $_lang . '/error.html');


// Asigna el mensaje
$_content->assign('MENSAJE', $error);


// Parse el error
$_content->parse('content.volver');
$_content->parse('content');


// Incluye el Layout
require_once 'includes/layout.php';
