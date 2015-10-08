<?php

/* Introducimos el idioma, el archivo y el contenido, y la funcion se encarga
 * de introducirlo y parsearlo al xtemplate, además mira si hay algún archivo
 * javascript asociado a la pagina
 */
function mostrar_pagina($archivo, $contenido) {

    global $titulos_web;
    global $nombres_web;
    global $link_nombres;

    global $_lang;
    global $_languages;
    global $_roles;

    // Creamos la template de la pagina, y le asignamos el titulo y contenido
    $pagina = new XTemplate("templates/$_lang/pagina.html");
    $pagina->assign('TITULO', $titulos_web[$archivo]);
    $pagina->assign('CONTENIDO', $contenido->text('content'));


    // Carga los nombres y enlaces del grupo, dpto y universidad
    $pagina->assign('NOMBRES', array_upper($nombres_web));
    $pagina->assign('LINK', array_upper($link_nombres));


    // Le asignamos el codigo javascript si lo tiene
    if (file_exists("scripts/$archivo.js")) {
        $pagina->assign('JAVASCRIPT', $archivo);
        $pagina->parse('main.javascript');
    }


    /// Incluimos los idiomas restantes
    unset($_languages[$_lang]);

    $i = 0;
    foreach ($_languages as $key => $value) {
        // Only for the first language
        if ($i++) {
            $pagina->parse('main.idioma.siguiente');
        }

        $pagina->assign('LANG', array(
            'VALUE' => $value,
            'URL'   => url(null, array('lang' => $key))
        ));
        $pagina->parse('main.idioma');
    }

    // Rellena los datos de la sesiones si está logueado
    if (isset($_SESSION['id_miembro'])) {
        // Obtiene el rol del usuario
        $pagina->assign('ROL', array(
            'NAME' => ($_SESSION['privilegios'] === INVITADO) ? $_roles[MIEMBRO] : $_roles[INVITADO],
            'URL'  => url(null, array('rol' => 1)),
        ));

        $pagina->assign('MIEMBRO', array(
            'ID'    => $_SESSION['id_miembro'],
            'URL'   => url('miembro_ver_ficha.php', array('id_miembro',  $_SESSION['id_miembro'])),
        ));
        $pagina->parse('main.miembro');

        if ($_SESSION['privilegios'] === ADMIN) {
            $pagina->parse('main.administracion');
        }

    } else {
        // Si no esta logueado muestra el acceso
        $pagina->parse('main.acceder');
    }


    // Parsea la pagina e imprime la pagina
    $pagina->parse('main');
    $pagina->out('main');
}

/**
 * Funcion que comprueba que un identificador recibido mediante $_GET existe, es
 * valido y se trata de un numero. En caso de no ser valido, muestra pagina de
 * error
 *
 * @param id Identificador a validar
 * @return id El identificador en caso de validacion correcta
 */
function validar_id($id)
{
    // Cargamos el array de errores
    GLOBAL $errors;

    // Comprueba si existe y si contiene algún caracter
    if (!isset($id) || strlen($id) == 0)
        error($errors['enlace'], 'El enlace no es válido, falta el identificador');

    // Comprobamos que se trata de un número, que es el identificador
    if ($id != (string) intval($id))
        error($errors['identificador'], 'El identificador que se ha introducido no es un número');

    return $id;
}


/*
 * Función para mostras lista para la seleccion de fecha
 */
function lista_fecha ($inicio, $fin, $seleccionado, &$contenido, $bloque)
{

// Recorremos los valores
    for ($i = $inicio; $i <= $fin; $i++) {
        // Marca el valor seleccionado
        $selected = ($seleccionado == $i) ? 'selected="selected"' : '';

        // Asigna e imprime el valor
        $contenido->assign('FECHA', $i);
        $contenido->assign('SELECCIONADO', $selected);
        $contenido->parse($bloque);
    }
}

/*
 * Convierte a mayúsculas las claves del array
 *
 */
function array_upper($array)
{

  return array_change_key_case($array, CASE_UPPER);
}

?>