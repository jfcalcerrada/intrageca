<?php

require_once __DIR__ . '/common/init.php';

// ejecuta autenticacion antes que nada
autenticar_usuario();

// Cierra la conexion con mysql
mysql_close($conexion);

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
