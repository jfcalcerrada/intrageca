<?php

/* Funcion que determina el idioma en funcion del párametro por url: lang
 * y verifica si este es correcto o no, existe o no en idiomas disponibles
 */
function determinar_idioma($idioma, $gen_idiomas_disp)
{

    // Comprobamos si existe el lenguaje, si no lo ponemos en español
    if(!isset($gen_idiomas_disp[$idioma])) {
        reset($gen_idiomas_disp);
        $idioma = key($gen_idiomas_disp);
    }

    return $idioma;
}

/* Introducimos el idioma, el archivo y el contenido, y la funcion se encarga
 * de introducirlo y parsearlo al xtemplate, además mira si hay algún archivo
 * javascript asociado a la pagina
 */
function mostrar_pagina($archivo, $contenido) {

    global $idioma;
    global $gen_idiomas_disp;
    global $gen_roles;
    global $titulos_web;
    global $nombres_web;
    global $link_nombres;
    global $intranet;

    // Creamos la template de la pagina, y le asignamos el titulo y contenido
    $pagina = new XTemplate("templates/$idioma/pagina.html");
    $pagina->assign('TITULO', $titulos_web[$archivo]);
    $pagina->assign('CONTENIDO', $contenido->text('content'));


    // Carga los nombres y enlaces del grupo, dpto y universidad
    $pagina->assign('NOMBRES', array_upper($nombres_web));
    $pagina->assign('LINK', array_upper($link_nombres));


    // Controla como inserta el idioma, el login y el cambio de rol
    $url = $_SERVER['REQUEST_URI'];
    $url = (strpos($url, '?') > 0) ? $url.'&' : $url.'?' ;


    // Control del idioma
    $idiomas = $gen_idiomas_disp;
    unset($idiomas[$idioma]);
    
    // Imprimimos el primer idioma
    $pagina->assign('URL', $url);
    $pagina->assign('IDIOMA', reset($idiomas));
    $pagina->assign('CLAVE', key($idiomas));
    $pagina->parse('main.idioma');

    // Imprimimos los siguientes idiomas
    while ($nombre_idioma = next($idiomas)) {
        $pagina->parse('main.idioma.siguiente');

        $pagina->assign('URL', $url);
        $pagina->assign('IDIOMA', $nombre_idioma);
        $pagina->assign('CLAVE', key($idiomas));
        $pagina->parse('main.idioma');
    }


    // Le asignamos el codigo javascript si lo tiene
    if (file_exists("scripts/$archivo.js")) {
        $pagina->assign('JAVASCRIPT', $archivo);
        $pagina->parse('main.javascript');
    }

    // Carga los datos de la publica o la intranet
    if (isset($_SESSION['id_miembro']) && $_SESSION['id_miembro'] >= 0) {

        // Parseamos el nombre de usuario y el boton de salir
        $pagina->assign('USUARIO', array_upper($_SESSION));

        // Seleccionamos el tipo de rol a mostrar
        $rol = ($_SESSION['privilegios'] != 0) ? $gen_roles['Invitado'] : $gen_roles['Usuario'];
        // Lo mostramos
        $pagina->assign('ROL', $rol);

        $pagina->parse('main.miembro');

        // Parseamos el menu de la intranet
        $pagina->parse('main.intranet');

        if ($_SESSION['privilegios'] == ADMIN)
          $pagina->parse('main.administracion');

    } else {
        /**
         * @nota: el acesso a la intranet por https
         * @nota: posibilidad de acceso a https como invitado
         */
        //$pagina->assign('URL', "$intranet$url");
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