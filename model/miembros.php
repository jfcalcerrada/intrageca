<?php

/**
 *
 */


/**
 * Obtiene un array con la información de todos los miembros del grupo
 *
 * @param integer $activo Selecciona o no los 'No activos' dependiendo de los
 * privilegios del usuario
 * @param string  $idioma Idioma sobre el que obtiene la información
 * @param array   $grupos El array con los diferentes tipos de grupos
 * 
 * @return array  Con todos los datos de los miembros
 */
function getMiembros($activo, $idioma = null, array $grupos = array())
{
    global $_lang;

    if ($idioma === null) {
        $idioma = $_lang;
    }

    // Consulta de los miembros
    $query = 'SELECT id_miembro, nombre, apellidos, categoria, activo 
              FROM miembros
              WHERE id_miembro > 0
                  AND activo >= :activo
                  AND categoria = :grupo
              ORDER BY activo DESC, apellidos ASC, nombre ASC';

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Asigna los parámetros
    $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);


    // Recorre los grupos y guardamos los miembros
    foreach ($grupos as $grupo => $grupo_lang) {
        // Inserta el grupo y realiza la consulta
        $stmt->bindParam(':grupo', $grupo, PDO::PARAM_STR);
        
        // Si hay errores, muestra el error
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            error('consulta', $error[2] . '; Consulta => ' . $query);
        }

        // Guarda los miembros en un array
        while ($miembro = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $miembros[$miembro['id_miembro']] = $miembro;
        }


        // Comprueba que haya devuelto resultados
        if (count($miembros) == 0) {
            error('consulta',
               'La consulta no ha devuelto resultados; Consulta => ' . $query);
        }
            
    } // foreach


    // Consulta del contenido dependiente del idioma
    $query = 'SELECT id_miembro, puesto, afiliacion 
              FROM miembro_idiomas 
              WHERE idioma = :idioma';

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Asigna los parámetros
    $stmt->bindParam(':idioma', $idioma, PDO::PARAM_STR);


    // Si hay errores, muestra el error
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    // Añade los datos de los miembros si están en el array anterior
    while ($miembro = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if (isset($miembros[$miembro['id_miembro']])) {
            $miembros[$miembro['id_miembro']] =
                array_merge($miembros[$miembro['id_miembro']], $miembro);
        }
    }

    return $miembros;
}
