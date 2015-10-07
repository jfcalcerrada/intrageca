<?php
// Inicializamos el archivo con el script
include "common/init.php" ;

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
$contenido->parse("content");

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>
