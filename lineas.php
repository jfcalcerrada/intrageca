<?php
// Inicializamos el archivo con el script
include('common/init.php');


//--------------------------------------------------------------------------
// lineas_investigacion.php
//
// Genera la página principal de lineas de investigacion y los exporta al template
// lineas_investigacion.html. Se genera una lista con el enlace a su página de
// información.
//--------------------------------------------------------------------------


// CONSULTA DE PROYECTOS
//------------------------------------------------------------------------------

// Cremos la consulta SQL
$consulta_lineas =
    "SELECT lineas.id_linea, titulo, descrip_corta, publico ".
    "FROM lineas LEFT JOIN linea_idiomas ".
    "ON lineas.id_linea = linea_idiomas.id_linea ".
    "WHERE idioma = '$idioma' ".
    "ORDER BY publico DESC";

// Realiza consulta para ver campos distintos
if (!($resultado_lineas = mysql_query($consulta_lineas)))
    ERR_muestra_pagina_error("Error en consulta: $consulta_lineas");
    
// Variable para controlar los proyectos no publicos

// Imprime para cada linea de investigacion una entrada
while($linea = mysql_fetch_array($resultado_lineas)) {

    //echo "id: {$linea['id_linea']}; titulo: {$linea['titulo']}; corta: {$linea['descrip_corta']}";

    // PUBLICOS??


    $contenido->assign("LINEA", $linea);
    $contenido->assign("STYLE", '');

    // Solo si se es el admin (IDM=0)
    if ($_SESSION['id_usuario'] == 0) {
        // Mostramos el boton para poder editar
        $contenido->assign("IDP", $proyectos['id_proyecto']);
        $contenido->parse("content.lineas.fila.editar");

        // Lo colocamos en el lado derecho
        $contenido->assign("STYLE", 'style="float: left;"');
    }

    // Lo imprimimos
    $contenido->parse("content.lineas.fila");

}

// Cerramos las lineas de investigacion
$contenido->parse("content.lineas");

// Mostramos el boton de añadir usuario a administrador
if($_SESSION['id_usuario'] == 0)
    $contenido->parse("content.anyadir");


// Cierra la conexion con mysql
mysql_close($conexion);

// Parsea el contenido
$contenido->parse("content");

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>
