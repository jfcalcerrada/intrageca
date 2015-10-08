<?php

/**
 * Funcion que muestra una pagina con el error producido. Guardar un log con los
 * errores y su respectiva descripcion
 *
 * @param string $mensaje : mensaje completo obtenido del array de errores
 * @param string $log : mensaje que se guardara en el log
 * @param boolean $database : para controlar si debe introducir el error o no
 */
function error($mensaje, $log = '', $url = '')
{
    global $_lang;


    /* GUARDAMOS LOS ERRORES EN LA BASE DE DATOS PARA MOSTRARLOS AL ADMIN */
    // Si hay mensaje para guardar
    if (strlen($log) > 0) {
        // Escapa los caracteres especiales
        $log_formateado = addslashes($log);

        // Obtenemos el nombre de usuario o si es invitado
        $usuario =
            (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : 'Invitado';

        // Crea la inserccion
        $insercion_log =
            "INSERT INTO errores(id, fecha, usuario, url, referido, log) ".
            "VALUES ('', '".date('Y-m-d H:i:s', time())."', ".
                "'$usuario' , '{$_SERVER['REQUEST_URI']}', ".
                "'{$_SERVER['HTTP_REFERER']}', '$log_formateado')";

        // Inserta el error en la base de datos
        mysql_query($insercion_log);

        // Cierra la conexion
        mysql_close();
    }

    // Creamos el objeto XTemplate para la pagina de error
    $contenido = new XTemplate(ROOT_FOLDER . "/templates/$_lang/error.html");


    /* CREA EL BOTON DE VOLVER EN FUNCIÓN DEL ERROR */
    // Si hay definida una redireccion
    if (strlen($url) > 0) {
        // Asignamos y parseamos
        $contenido->assign('URL', $url);
        $contenido->parse('content.redirigir');

    // Si no hay definida redireccion en url, mostramos volver
    } else {
        $contenido->parse('content.volver');
    }


    /* MOSTRAMOS LA PAGINA DEL ERROR PARA LOS USUARIOS */
    // Convertimos el mensaje a html
    $mensaje = nl2br(htmlentities($mensaje));

    // Si es el administrador, mostramos un mensaje mas completo
    if ($_SESSION['privilegios'] == ADMIN)
        $mensaje = nl2br(htmlentities($log));

    // Asigna mensaje
    $contenido->assign('MENSAJE', $mensaje);

    // Parseamos la pagina
    $contenido->parse('content');

    // Introducimos en el template general e imprimimos
    mostrar_pagina('common_error', $contenido);

    // Mata el proceso
    die();
}


//--------------------------------------------------------------------------
// Function: muestra_pagina_error
//
// Muestra una página de error con el mensaje que se le pasa como parametro.
//
// Parametros de entrada
//   $mensaje : El mensaje de error que se muestra.
//
function ERR_muestra_pagina_error($mensaje)
{
    global $_lang;
    GLOBAL $errors;

    // Comprobamos si el mensaje existe en el array
    if(isset($errors[$mensaje]))
    $mensaje = $errors[$mensaje];

    // Convertimos el mensaje a html
    $mensaje_html = nl2br(htmlentities($mensaje));

    // Creamos el objeto XTemplate para la pagina de error
    $contenido = new XTemplate(ROOT_FOLDER . "/templates/$_lang/error.html");

    // Asigna mensaje
    $contenido->assign("MENSAJE", $mensaje_html);

    // Asigna la pagina de donde hemos venido
    $contenido->assign("REFERER", $_SERVER['HTTP_REFERER']);
    $contenido->parse("content");

    // Introducimos en el template general e imprimimos
    mostrar_pagina('common_error', $contenido);
    @mysql_close();
    exit;
}

//--------------------------------------------------------------------------
// Function: muestra_pagina_mensaje
//
// Muestra una página de mensaje que se le pasa como parametro.
//
// Parametros de entrada
//   $mensaje : El mensaje que se muestra.
//
function ERR_muestra_pagina_mensaje($mensaje, $dir_idioma)
{
    global $_lang;
    global $errors;

    // Convertimos el mensaje a html
    $mensaje_html = nl2br(htmlentities($errors[$mensaje]));

    // Creamos el objeto XTemplate para la pagina de error
    $contenido = new XTemplate (ROOT_FOLDER . "/templates/$_lang/error.html");

    // Asigna mensaje
    $contenido->assign("MENSAJE", $mensaje_html);

    // Asigna la pagina de donde hemos venido
    $contenido->assign("REFERER", $_SERVER['HTTP_REFERER']);
    $contenido->parse("content");

    // Introducimos en el template general e imprimimos
    mostrar_pagina('common_mensaje', $contenido);
    @mysql_close();
    exit;
}
