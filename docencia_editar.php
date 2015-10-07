<?php
// Inicializamos el archivo con el script
include('common/init.php');

include("docencia_insertar.php");


/**
 * @name docencia_editar.php
 *
 * @desc Página para crear/editar las diferentes asignaturas pudiendo añadirles
 * miembros que las imparten y coordinadores
 * @access Privado: cualquier Miembro
 * @param ida Identificador de asignatura
 */


/* VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA */
// Comprueba si existe el $_POST;
if (isset($_POST['actualizar']) && $_POST['actualizar'] == 1) {
    // Llama a la funcion de insertar/actualizar
    $id_asignatura = docencia_insertar($_POST);

} else {
    // Verifica la validez del parametro
    $id_asignatura = validar_id($_GET['ida']);
}


// Comprobamos el acceso, solo permitido a miembros y administrador
acceso_miembros('docencia.php');


/* SELECCIONA EL IDIOMA */
// Seleccionamos el idioma en que se esta mostrando el
if (isset($_POST['idioma_actual']) && strlen($_POST['idioma_actual'])) {
    $asignatura_idioma = $_POST['idioma_actual'];
} else {
    $asignatura_idioma = $idioma;
}


/* CONSULTA LOS DATOS DE LA ASIGNATURA */
// Si el identificador es distinto de 0, mostramos dicha asignatura
if ($id_asignatura > 0) {
    // Consulta los datos de la asignatura
    $consulta_asignatura =
        "SELECT id_asignatura, link, publico ".
        "FROM asignaturas ".
        "WHERE id_asignatura = $id_asignatura ";

    // Realizamos la consulta y comprobamos que no da errores
    $resultado_asignatura = mysql_query($consulta_asignatura)
        or error($errors['consulta'], "Error en la consulta: $consulta_asignatura");

    // Comprueba que existe
    if (mysql_num_rows($resultado_asignatura) == 0)
        error($errors['asignatura'], 'Problema en el archivo docencia editar');

    // Crea el array con los datos de la asignatura
    $asignatura = mysql_fetch_assoc($resultado_asignatura);


    // Crea la consulta relativa a los datos del idioma
    $consulta_idioma =
        "SELECT nombre ".
        "FROM asignatura_idiomas ".
        "WHERE id_asignatura = $id_asignatura ".
            "AND idioma = '$asignatura_idioma'";

    // Realizamos la consulta y comprobamos que no da errores
    $resultado_idioma = mysql_query($consulta_idioma)
        or error($errors['consulta'], "Error en la consulta: $consulta_idioma");

    // Comprueba si existen los datos para el idioma
    if (mysql_num_rows($resultado_idioma) != 0) {
        $asignatura_idiomas = mysql_fetch_assoc($resultado_idioma);
    // Si no existen crea un array vacio
    } else {
        $asignatura_idiomas = array();
    }


    // Une los dos array de la asignatura
    $asignatura = array_merge($asignatura, $asignatura_idiomas);

    // Corrige el valor de publico
    $asignatura['publico'] = ($asignatura['publico'] == 1) ? 'checked="checked"' : '';


// Si el ida es 0 se esta insertando una nueva asignatura
} else {
    $asignatura = array ('id_asignatura' => '0');

}


/* MUESTA EL BOTON DE ELIMINAR */
// Si la asignatura no es nueva
if ($id_asignatura != 0) {
    $contenido->assign('IDA', $id_asignatura);
    $contenido->assign('content.menu');
}



/* MUESTRA LOS DIFERENTES IDIOMAS DISPONIBLES */
// Asigna el campo oculto con el codigo actual, en el que se muestran los datos
$contenido->assign('COD_IDIOMA', $asignatura_idioma);

// Imprime los idiomas disponibles
foreach($gen_idiomas_disp as $clave_idioma => $texto_idioma) {
    // Selecciona el idioma que se esta editanto
    $selected = ($asignatura_idioma == $clave_idioma) ? 'selected="selected"': '';

    // Asigna la lista
    $idioma_lista= array ( 'CLAVE'        => $clave_idioma,
                           'TEXTO'        => $texto_idioma,
                           'SELECCIONADO' => $selected);

    // insertalo en página
    $contenido->assign('IDIOMA', $idioma_lista);
    $contenido->parse('content.asignatura.idioma');
}


/* IMPRIME LOS DATOS DE LA ASIGNATURA */
// Prepara el array
$asignatura = array_change_key_case($asignatura, CASE_UPPER);

// Asigna y parsea
$contenido->assign('ASIGNATURA', $asignatura);
$contenido->parse('content.asignatura.datos');



/* OBTIENE LOS MIEMBROS QUE IMPARTEN DICHA ASIGNATURA */
// Crea la consulta para la obtencion de los miembros
$consulta_miembros =
    "SELECT miembros.id_miembro, nombre, apellidos, coordinador ".
    "FROM asignatura_miembros LEFT JOIN miembros ".
        "ON asignatura_miembros.id_miembro = miembros.id_miembro ".
    "WHERE id_asignatura = '$id_asignatura' ".
    "ORDER BY coordinador DESC, apellidos ASC";

// Realizamos la consulta y comprobamos que no da errores
$resultado_miembros = mysql_query($consulta_miembros)
    or error($errors['consulta'], "Error en la consulta: $consulta_miembros");


// Obtiene el numero de miembros que la imparten
$numero_miembros = mysql_num_rows($resultado_miembros);

// Indice para el controla de los miembros en el formulario
$indice = 1;

// Muestra los miembros que imparten dicha asignatura
while ($miembro = mysql_fetch_assoc($resultado_miembros)) {
    // Añade el indice
    $miembro['indice'] = $indice++;

    // Si es coordinador lo marca
    $miembro['coordinador'] =
      ($miembro['coordinador'] == 1) ? 'checked="checked"' : '';

    // Prepara el array
    $miembro = array_change_key_case($miembro, CASE_UPPER);

    // Asigna y parse
    $contenido->assign('MIEMBRO', $miembro);
    $contenido->parse('content.asignatura.miembros.miembro');
}


/* CREA LA LISTA PARA AÑADIR MIEMBROS */
// Crea la consulta de los miembros que no la imparten
$consulta_miembros =
    "SELECT id_miembro, nombre, apellidos ".
    "FROM miembros ".
    "WHERE id_miembro NOT IN ".
            "(SELECT id_miembro ".
            "FROM asignatura_miembros ".
            "WHERE id_asignatura = $id_asignatura) ".
    "ORDER BY apellidos ASC";

// Realizamos la consulta y comprobamos que no da errores
$resultado_miembros = mysql_query($consulta_miembros)
    or error($errors['consulta'], "Error en la consulta: $consulta_miembros");

// Muestra los miembros que imparten dicha asignatura
while ($miembro = mysql_fetch_assoc($resultado_miembros)) {
    // Prepara el array
    $miembro = array_change_key_case($miembro, CASE_UPPER);

    // Asigna y parse
    $contenido->assign('MIEMBRO', $miembro);
    $contenido->parse('content.asignatura.miembros.seleccion');
}

// Cierra los miembros
$contenido->assign('NUMERO_MIEMBROS', $numero_miembros);
$contenido->parse('content.asignatura.miembros');


// Cierra la asignatura
$contenido->parse('content.asignatura');

// Cierra la conexion con mysql
mysql_close($conexion);


/* MUESTRA LA PAGINA */
// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);f

?>