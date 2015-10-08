<?php

/**
 * miembro_ver_ficha.php
 *
 * Pagina que genera la ficha del miembro solicidado. Para ello necesita
 * que sea introducido por parámetro el Identificador de Miembro.
 * 
 * @access  Público   Para los miembros "Activos"
 * @access  Privado   Para los miembros "Activos" y "No activos"
 *
 * @param   $id_miembro Identificador del Miembro
 *
 */


// Carga los includes de la cabecera
require_once 'includes/bootstrap.php';

// Carga el modelo
require_once 'model/includes/miembros.php';
require_once 'model/' . $_file . '.php';



// Extrae las variables necesarias para el script
extract(arrayKeys($_REQUEST, array('id_miembro')));


// Verifica si es correcto
validateId($id_miembro);


/* CONSULTA LOS DATOS DEL MIEMBRO */
// Si es un invitado solo puede ver los miembros activos, es decir 1
$activo = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;


// Obtenemos los datos del miembro
$miembro = getMiembroDatos($id_miembro, $_lang, $activo);



// Carga la categoría en función del idioma
$miembro['categoria'] = $_member['grupos'][$miembro['categoria']];

// Formatea la fecha de incorporacion
$miembro['fecha_incorporacion'] =
    date_format(date_create($miembro['fecha_incorporacion']), 'd-m-Y');

// Sustituye las nuevas líneas por <br />
$miembro['direccion'] = nl2br($miembro['direccion']);


// Comprueba si el miembro imparte alguna asignatura
$docencia = getMiembroDocencia($id_miembro);


// Obtiene los parámetros de la query de las publicaciones
$publicaciones = getEnlaceMiembroPublicaciones($id_miembro);

//TODO: cambiar $curriculum en la vista
// Comprueba si tiene curriculum
$hasCurriculum = (!(empty($miembro['link_curriculum'])) || !(empty($miembro['curriculum'])));



// Incluye la vista de la pagin
require_once 'vista/' . $_file . '.php';

