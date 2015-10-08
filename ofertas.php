<?php

require_once 'common/init.php';

// Autenticamos al usuario
autenticar_usuario();

/**
 * ofertas.php
 *
 * Genera una pagina con el listado de las diferentes ofertas suministradas por
 * el grupo. Las ofertas tendran asociado un miembro, y podrán estar activas o
 * no activas, estas ultimas no visibles a los invitados. También tendrán una
 * fecha de caducidad, a partir de la cual no serán mostradas en la web publica
 * y pasarna a ser... no activos??
 *
 */


/*
 * LISTADO DE TODAS LAS OFERTAS DEL GRUPO
 */
// Si es un invitado solo puede ver los proyectos publicos, es decir 1
$activa = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

// Consulta de los proyectos
$consulta_ofertas =
    "SELECT ofertas.id_oferta, id_miembro, titulo, descripcion, activa, ".
        "fecha_caducidad ".
    "FROM ofertas LEFT JOIN oferta_idiomas ".
        "ON ofertas.id_oferta = oferta_idiomas.id_oferta ".
    "WHERE idioma = '$idioma' ".
        "AND activa >= $activa ".
    "ORDER BY activa DESC, titulo ASC";

// Realizamos la consulta y comprobamos que no da errores
$resultado_ofertas = mysql_query($consulta_ofertas)
    or error($errors['consulta'], "Error en la consulta: $consulta_ofertas");

// Variable para controlar los proyectos no publicos
$ultima_oferta = 1;

// Imprime para cada proyecto
while ($oferta = mysql_fetch_array($resultado_ofertas)) {

    // Controlamos si entramos en los proyectos no publicos
    if($ultima_oferta != $oferta['activa'] && $oferta['activa'] == 0) {
        // Inserta el resto de proyectos activos
        $_content->parse("content.ofertas");

        // Inserta cabecera de no activos
        $_content->parse("content.ofertas.cabecera_noactiva");

        // Actualiza valor de ultimo proyecto
        $ultima_oferta = $oferta['activa'];
    }

    // Prepara el array a parsear
    $oferta = array_change_key_case($oferta, CASE_UPPER);

    // Imprime el proyecto
    $_content->assign('OFERTA', $oferta);


//    // Solo si se es el admin (IDM=0) o responsable, tendremos que hacer una consulta
//    $sql_responsable =
//        "SELECT id_miembro ".
//        "FROM proyecto_miembros ".
//        "WHERE id_proyecto = {$proyectos['id_proyecto']} ".
//            "AND id_miembro = {$_SESSION['id_usuario']} ".
//            "AND responsable = 1";
//
//    $resultado_responsable = mysql_query($sql_responsable);
//    if($_SESSION['privileges'] == ADMIN || mysql_num_rows($resultado_responsable)) {
//        // Mostramos el boton para poder editar
//        $contenido->assign("IDP", $proyectos['id_proyecto']);
//        $contenido->parse("content.proyectos.fila.editar");
//
//        // Lo colocamos en el lado derecho
//        $contenido->assign("STYLE", 'style="float: left;"');
//    }

    // Lo imprimimos
    $_content->parse('content.ofertas.oferta');
}

// Cierra los ofertas
$_content->parse('content.ofertas');

// Cierra la conexion con mysql
mysql_close($conexion);


/*
 * BOTON AÑADIR OFERTAS SOLO ADMIN
 */
// Mostramos el boton de añadir oferta si es el administrador
if($_SESSION['privilegios'] == ADMIN)
    $_content->parse('content.anyadir');


/*
 * MUESTRA LA PAGINA
 */
// Parsea el contenido
$_content->parse("content");

// Muestra la pagina final
mostrar_pagina($archivo, $_content);
