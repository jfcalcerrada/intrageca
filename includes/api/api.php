<?php

/**
 * Funcion que a partir de la variable de PHP $_REQUEST y un array con un
 * listado de claves realiza la intersecci�n para as� poder recuperar con
 * extract las variables necesarias para un m�s f�cil acceso
 *
 * @param array $request Array con los par�metros pasados en la llamada a la url
 * @param array $array   El array que queremos modificar
 * 
 * @return array Devuelve el array cambiado
 */
function arrayKeys($request, $array)
{
    return array_intersect_key($request, array_fill_keys($array, ''));
}


/**
 * A partir de un array de datos, t�picamente los datos de una tabla, y una
 * consulta a realizar sobre dicha tabla, prepara un array con las claves
 * modificadas para poder pasarlo como par�metro a PDO->STMT->execute()
 *
 * @param array  $array Array con los datos a evaluar
 * @param string $query Cadena de texto PDO:sql para obtener las claves
 *
 * @return array Array formateado con las claves
 */
function arraySql($array, $query)
{
    foreach ($array as $key => $value) {
        if (strpos($query, ':'.$key) !== false) {
            $arraySql[':'.$key] = $value;
        }
    }

    return $arraySql;
}


/**
 * Funci�n que cambia a may�sculas las claves de un array
 *
 * @param array $array El array que queremos modificar
 *
 * @return array Devuelve el array cambiado
 */
function arrayUpper($array)
{
    // TODO remove
    if ($array === null) {
        return array();
    }

    return array_change_key_case($array, CASE_UPPER);
}


/**
 * Funci�n que comprueba si el identificador dado es un numero
 *
 * @param integer $id El identificador que queremos validar
 * 
 * @return integer Devuelve el identificador si es un numero
 */
function validateId($id)
{
    if (!ctype_digit($id)) {
        error('identificador', 'Se ha introducido el siguiente id ' . $id);
    }
    
    return $id;
}
