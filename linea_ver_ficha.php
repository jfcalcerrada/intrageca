<?php

require_once __DIR__ . '/common/init.php';

// ejecuta autenticacion antes que nada
autenticar_usuario();

//--------------------------------------------------------------------------
// linea_investigacion_ver_ficha.php
//
// Genera la ficha de una linea en cuestion. Para ello necesita que
// se le pase como parámetro la identidad de linea. Si la identidad
// no está disponible, se muestra la página de error.
//
// Parametros de entrada
//  idl : Identificador de linea
//--------------------------------------------------------------------------

// Comprobamos si existe el campo idm en la url y si esta asignado
if (!isset($_GET['idl']) || strlen($_GET['idl']) == 0)
    ERR_muestra_pagina_error('enlace');


// CONSULTA DE DATOS DE LA LINEA
//------------------------------------------------------------------------------

// Definicion de consultas de la base de datos
$consulta_linea =
    "SELECT titulo, activo, publico, descripcion ".
    "FROM lineas LEFT JOIN linea_idiomas ".
    "ON lineas.id_linea = linea_idiomas.id_linea ".
    "WHERE lineas.id_linea = '{$_GET['idl']}' ".
        "AND idioma = '$idioma'";

// Realizamos la consulta de la linea y comprobamos
if ( !($resultado_linea = mysql_query($consulta_linea)) )
    ERR_muestra_pagina_error("Error en consulta: $consulta_linea");

// Chequea si hay una linea con dicho identificador
if (mysql_num_rows($resultado_linea) == 0)
    ERR_muestra_pagina_error('proyecto'); //!!!


// Obtenemos el resultado de la busqueda
$linea = mysql_fetch_array($resultado_linea);


// *************************
// MOSTRAMOS EL RESULTADO!!!!
//print_r($linea);
echo "<br /><br />";

// CONSULTA DE LOS PROYECTOS DE LA LINEA
//------------------------------------------------------------------------------
// Creamos la consulta
$consulta_proyectos =
    "SELECT linea_proyectos.id_proyecto, publico, estado, titulo, descrip_corta ".
    "FROM linea_proyectos ".
    "LEFT JOIN proyectos ".
        "ON linea_proyectos.id_proyecto = proyectos.id_proyecto ".
    "LEFT JOIN proyecto_idiomas ".
        "ON proyectos.id_proyecto = proyecto_idiomas.id_proyecto ".
    "WHERE linea_proyectos.id_proyecto = {$_GET['idl']} ".
        "AND proyecto_idiomas.idioma = '$idioma' ".
    "ORDER BY publico DESC";

// Realizamos la consulta y comprobamos si esta bien
if ( !($resultado_proyectos = mysql_query($consulta_proyectos)))
    ERR_muestra_pagina_error("Error en consulta: $consulta_proyectos");

// Realizamos el bucle para mostrar los proyectos
while ($proyecto = mysql_fetch_array($resultado_proyectos)) {
    print_r($proyecto);
    echo "<br /><br />";
}
echo "<br />";


// CONSULTA MIEMBROS
$consulta_gen =
    "SELECT miembros.id_miembro, miembros.nombre, responsable ".
    "FROM linea_miembros LEFT JOIN miembros ".
    "ON linea_miembros.id_miembro = miembros.id_miembro ".
    "WHERE id_linea = {$_GET['idl']} AND categoria =";

// Para cada una de las categorias busca los miembros
foreach ($mbr_rel_grupos as $grupo => $grupo_web) {
    // Construye consulta
    $consulta_miembros = "$consulta_gen '$grupo'";

    // Realiza consulta de miembro
    if (!($resultado_miembros = mysql_query($consulta_miembros)))
        ERR_muestra_pagina_error("Error en consulta: $consulta_miembros");

    // Comprueba si hay miembros en el proyecto
    if (mysql_num_rows($resultado_miembros) > 0) {


        while ($miembro = mysql_fetch_array($resultado_miembros)){
            // Si es el responsable lo mostramos como tal
            if ($miembro['responsable'] == 1)
                $_content->parse("content.lista_miembros.fila.responsable");

            // Crea el array de valores
            $lista_valores = array(
                'IDM' => $miembro['id_miembro'],
                'NOMBRE' => $miembro['nombre'],
                'CATEGORIA' => $grupo_web);

            // asigna valores a la pagina
            $_content->assign("LISTAM", $lista_valores);
            $_content->parse("content.lista_miembros.fila");
        }
    }
}

// imprime tabla
$_content->parse("content.lista_miembros");


// Cierra la conexion con mysql
mysql_close($conexion);

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
