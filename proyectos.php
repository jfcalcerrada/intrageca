<?php

require_once 'common/init.php';


/**
 * proyectos.php
 *
 * Genera una pagina con el listado de proyectos del grupo. Cada proyecto es un
 * enlace a su pagina de informacion.
 * 
 */


// NOTA! comprobar los proyectos si han acabado de fecha, etc
// añadir otro estado??? no publico, publico, acabado o algo asi?


/*
 * ACTUALIZACION DE ESTADOS DE LOS PROYECTOS
 */

// Si la fecha de inicio es mayor que la de hoy => Concedido
$actualizacion_estados =
    "UPDATE proyectos ".
    "SET estado = 0 ".
    "WHERE fecha_inicio > '".date('Y-m-d', time())."'";

mysql_query($actualizacion_estados);

// Si esta concedido y la fecha de inicio es mayor que la de hoy => En curso
$actualizacion_estados =
    "UPDATE proyectos ".
    "SET estado = 1 ".
    "WHERE estado = 0 ".
        "AND fecha_inicio <= '".date('Y-m-d', time())."'";

mysql_query($actualizacion_estados);

// Si esta en curso y la fecha de fin es mayor que la de hoy => Terminado
$actualizacion_estados =
    "UPDATE proyectos ".
    "SET estado = 2 ".
    "WHERE estado = 1 ".
        "AND fecha_inicio < fecha_fin ".
        "AND fecha_fin <= '".date('Y-m-d', time())."'";

mysql_query($actualizacion_estados);



/*
 * CONSULTA DE TODOS LOS PROYECTOS
 */
// Si es un invitado solo puede ver los proyectos publicos, es decir 1
$publico = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

//// Buscamos los proyectos que esten activos o en curso o terminados en los
//// ultimos dos meses
//$consulta_proyectos =
//    "(SELECT id_proyecto FROM proyectos WHERE activo < 2) ".
//    "UNION ".
//    "(SELECT id_proyecto FROM proyectos
//    WHERE activo = 2 AND fecha_fin >= '".date('Y-m', time()-32*24*60*60)."')";


// Consulta de los proyectos
$consulta_proyectos =
    "SELECT proyectos.id_proyecto, titulo, descrip_corta, publico, ".
        "fecha_inicio, fecha_fin, estado ".
    "FROM proyectos LEFT JOIN proyecto_idiomas ".
        "ON proyectos.id_proyecto = proyecto_idiomas.id_proyecto ".
    "WHERE idioma = '$idioma' ".
        "AND publico >= $publico ".
    "ORDER BY publico DESC, estado ASC, fecha_inicio ASC";

// Realizamos la consulta y comprobamos que no da errores
$resultado_proyectos = mysql_query($consulta_proyectos)
    or error($errors['consulta'], "Error en la consulta: $consulta_proyectos");

// Variable para controlar el estado de los proyectos
$proyecto_estado = -1;
$proyectos_por_estado = 0;

// Variable para controlar los proyectos no publicos
$proyecto_publico = 1;

// Imprime para cada proyecto
while ($proyecto = mysql_fetch_array($resultado_proyectos)) {

    // Si el estado es diferente, imprime los anteriores y muestra cabecera
    if ($proyecto_estado != $proyecto['estado'] && $proyecto['publico'] == 1) {

        // Si ha imprimido alguno
        if ($proyectos_por_estado != 0) {
            // Inserta el resto de proyectos activos
            $_content->parse("content.proyectos");
        }

        // Inserta la cabecera correspondiente
        switch ($proyecto['estado']) {
            case 0:
                // Inserta la cabecera
                $_content->parse('content.proyectos.concedido');
                break;
            case 1:
                // Inserta la cabecera
                $_content->parse('content.proyectos.encurso');
                break;
            case 2:
                // Inserta la cabecera
                $_content->parse('content.proyectos.terminado');
                break;
        }

        // Cambiamos el estado actual
        $proyecto_estado = $proyecto['estado'];

        // Reseteamos a 0 el contador
        $proyectos_por_estado = 0;
    }

    // Controlamos si entramos en los proyectos no publicos
    if($proyecto_publico != $proyecto['publico'] && $proyecto['publico'] == 0) {
        // Inserta el resto de proyectos activos
        $_content->parse("content.proyectos");

        // Inserta cabecera de no activos
        $_content->parse("content.proyectos.cab_nopublico");
        
        // Actualiza valor de ultimo proyecto
        $proyecto_publico = $proyecto['publico'];
    }

    // Prepara el array a parsear
    $proyecto = array_change_key_case($proyecto, CASE_UPPER);

    // Imprime el proyecto
    $_content->assign('PROYECTO', $proyecto);

    // Aumenta el contador
    $proyectos_por_estado++;


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
    $_content->parse("content.proyectos.proyecto");
}

// Cierra los proyectos
$_content->parse("content.proyectos");

// Cierra la conexion con mysql
mysql_close($conexion);


/*
 * BOTON AÑADIR PROYECTOS SOLO ADMIN
 */
// Mostramos el boton de añadir proyecto si es el administrador
if($_SESSION['privilegios'] == ADMIN)
    $_content->parse("content.anyadir");


// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
