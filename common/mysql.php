<?php

/**
 * Funcion que sirve para conectar con la base de datos
 * @param string  BASE_DATOS  Base de datos
 * @param string  USER_BD Usuario
 * @param string  PASS_BD Contraseña
 * @return  connection
 */
function conectar_mysql($BASE_DATOS, $USER_BD, $PASS_BD)
{

    global $errors;

    // Conecta a Base de Datos MySQL
    $conexion = @mysql_connect("localhost", $USER_BD, $PASS_BD);

    // Selecciona base de datos
    @mysql_select_db($BASE_DATOS);

    // Si se produce un error, lo mostramos
    if (!$conexion)
        error($errors['conexion']);

    return $conexion;
}

?>