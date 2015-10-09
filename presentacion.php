<?php

require_once 'common/init.php';

//--------------------------------------------------------------------------
// presentacion.php
//
// Genera la página de presentacion del grupo en funcion del lenguaje
// seleccionado
//--------------------------------------------------------------------------

// TODO EL CONTENIDO DE ESTA PAGINA ESTA EN LA PLANTILLA


// Cierra la conexion con mysql
mysql_close($conexion);

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
