<?php

/**
 *
 */


// Carga la plantilla
$_content = new XTemplate('templates/' . $_lang . '/' . $_file . '.html');

// Variables de apoyo
$grupo        = '';
$ultimoActivo =  1;

// Recorremos los miembros
foreach ($miembros as $miembro) {

    // Mira si hay cambio de grupo o 'activo'
    $cambioGrupo  = ($grupo != $miembro['categoria']);
    $cambioActivo = ($ultimoActivo != $miembro['activo']);

    // Si cambia de grupo o cambiar de 'activo', parsea el grupo y cambia activo
    if (($cambioGrupo || $cambioActivo) && !empty($grupo)) {
        $ultimoActivo = $miembro['activo'];
        $_content->parse('content.grupo');
    }

    // Si cambia de grupo, agrega la cabecera
    if ($cambioGrupo) {
        $grupo = $miembro['categoria'];
        $_content->assign('GRUPO', $_member['grupos'][$grupo]);
        $_content->parse('content.grupo.nombre');
    }

    // Si cambia a no activos y es No activo, agrega la cabecera
    if ($cambioActivo && !$ultimoActivo) {
        $_content->parse('content.grupo.noactivos');
    }

    // Cargamos los datos que existan
    $miembro['datos'] .= ($miembro['apellidos'])
        ? $miembro['apellidos'].', ' : '';

    $miembro['datos'] .= ($miembro['nombre'])
        ? $miembro['nombre'] : '';

    $miembro['datos'] .= ($miembro['puesto'])
        ? ' - '.$miembro['puesto'] : '';
        
    $miembro['datos'] .= ($miembro['afiliacion'])
        ? ' - '.$miembro['afiliacion'] : '';
        

    // Imprimimos el miembro
    $_content->assign('MIEMBRO', arrayUpper($miembro));
    $_content->parse('content.grupo.miembro');
}

// Cierra el último grupo
$_content->parse('content.grupo');


// Muestra el botón Añadir si es Administrador
if ($_SESSION['privilegios'] == ADMIN) {
    $_content->parse('content.anyadir');
}


// Parse todo el contenido
$_content->parse('content');


// Incluye el Layout
require_once 'includes/layout.php';


?>
