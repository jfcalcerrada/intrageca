<?php
// Inicializamos el archivo con el script
include('common/init.php');
include('autenticacion.php');
// Autenticamos al usuario
autenticar_usuario();


/**
 *
 */

/*
 * OBTENEMOS LOS PARAMETROS
 */
// Obtenemos los argumentos de la busqueda, ello nos evita tener que ir rehaciendo
// la búsqueda para introducirla en los cambios de pagina.
$argumentos = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?'));

// Obtenemos el numero de pagina en el que nos encontramos
if (isset($_GET['pagina']) && strlen($_GET['pagina']) > 0
    && ($_GET['pagina'] != (string) intval($_GET['pagina']))) {

    $numero_pagina = $_GET['pagina'];
} else {
    $numero_pagina = 1;
}


// LISTA DE PARAMETROS DE BUSQUEDA
//------------------------------------------------------------------------------
// Cremos la variable de la consulta
$consulta_proyectos =
    "SELECT * ".
    "FROM proyectos ".
    "LEFT JOIN proyecto_idiomas ".
        "ON proyectos.id_proyecto = proyecto_idiomas.id_proyecto ".
    "WHERE idioma = '$idioma'";


// Miramos la logica de la busqueda, por defecto AND
if (isset($_GET['logica']) && strlen($_GET['logica']) > 0 ) {
    $logica = $_GET['logica'];
} else {
    $logica = "AND";
}

// Miramos si se buscan publicos o no o ambos
if (isset($_GET['publico']) && strlen($_GET['publico']) > 0 ) {
    if ($_GET['publico'] == 0 || $_GET['publico'] == 1) {
        $consulta_proyectos .= " AND publico = {$_GET['publico']}";
    } elseif ($_GET['publico'] != 2) {
        echo "error en publico"; die;
    }
}

// Miramos si se han introducido fechas, si el proyecto no acabo antes de la
// fecha es que esta dentro del rango y comprobamo si esta en curso.
if(isset($_GET['desde']) && strlen($_GET['desde']) > 0) {
    $consulta_proyectos .= 
        " AND (fecha_fin <= {$_GET['desde']} OR fecha_fin <= fecha_inicio)";
}

if(isset($_GET['hasta']) && strlen($_GET['hasta']) > 0) {
    $consulta_proyectos .= " AND fecha_inicio >= {$_GET['hasta']}";
}


//SELECT id_proyecto, count(miembros.id_miembro) AS num_miembros FROM proyecto_miembros LEFT JOIN miembros ON proyecto_miembros.id_miembro = miembros.id_miembro WHERE miembros.nombre LIKE '%castillo%'GROUP BY id_proyecto


// Solo si hay miembros en la busqueda
if (isset($_GET["miembro1"]) && strlen($_GET["miembro1"]) > 0) {
    $consulta_miembros =
        "SELECT id_proyecto ".
        "FROM proyecto_miembros ".
        "LEFT JOIN miembros ".
            "ON proyecto_miembros.id_miembro = miembros.id_miembro ".
        "WHERE miembros.nombre LIKE '%{$_GET["miembro1"]}%'";

    // Seleccionamos la logica por defecto, AND
    $logica_mie = "AND";
    // Comprobamos si se ha seleccionado logica
    if (isset($_GET['logica_mie']) && strlen($_GET['logica_mie']) > 0 ) {
        // Comprobamos que esta es correcta
        if ($logica_mie == 'OR' || $logica_mie == 'AND') {
            $logica_mie = $_GET['logica_mie'];
        }
    }

    // Recorremos los miembros
    $i = 2;
    for ($i = 2; isset($_GET["miembro$i"]) && strlen($_GET["miembro$i"]) > 0;
            $i++) {
            
        // Los añadimos a la busqueda
        $consulta_miembros .=
            " OR miembros.nombre LIKE '%{$_GET["miembro$i"]}%'";

        // Guardamos el numero de miembros
        $miembros = $i;
    }

    // Los agrupamos por proyecto
    $consulta_miembros .= " GROUP BY proyecto_miembros.id_proyecto";

    // Si tienen que estar todos comprobamos que los hay
    if ($logica_mie == 'AND') {
        $consulta_miembros .= " HAVING count(miembros.id_miembro) >= $miembros";
    }

    $consulta_proyectos .=
        " $logica proyectos.id_proyecto IN ($consulta_miembros)";
}


// id_proyecto 	id_pr_bibtex 	publico 	activo 	fecha_inicio 	fecha_fin
// financiador 	importe 	moneda 	mostrar_importe 	link_proyecto
// id_proyecto 	idioma 	titulo 	descrip_corta 	descripcion

echo $consulta_proyectos;

// Si hemos creado la consulta de campos, la insertamos
if (isset($consulta_campos)) {
    // Eliminamos el ultimo conector
    //$consulta_campos = substr()
    // La insertamos en la busqueda

}


?>