<?php

/**
 *
 */


/**
 * Obtiene los datos de un miembro a partir de su identificado, el idioma y si
 * está activo o no.
 *
 * @param integer $id_miembro Identificador del miembro
 * @param string  $idioma     Idioma de los datos
 * @param integer $activo     Indica si es activo o no
 *
 * @return array Los datos del miembro
 */
function getMiembroDatos($id_miembro, $idioma = null, $activo = 0)
{
    global $_lang;

    if ($idioma === null) {
        $idioma = $_lang;
    }

    // Se crea la consulta
    $query = 'SELECT id_miembro, nombre, apellidos, categoria, activo,
                  direccion, telefono, fax, email, link_foto,
                  fecha_incorporacion
              FROM miembros
              WHERE id_miembro = :id_miembro
                  AND activo >= :activo';

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Asigna los parámetros
    $stmt->bindParam(':id_miembro', $id_miembro, PDO::PARAM_INT);
    $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);

    // Realiza la consulta, si hay error lo muestra
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; $Consulta => ' . $query);
    }

    // Si no hay resultados, muestra error
    if (!($miembro = $stmt->fetch(PDO::FETCH_ASSOC))) {
        error('miembro',
            'No existe el miembro ' . $id_miembro . ' y activo ' . $activo);
    }



    // Se crea la consulta
    $query = 'SELECT puesto, afiliacion, curriculum, link_curriculum 
              FROM miembro_idiomas
              WHERE id_miembro = :id_miembro
                  AND idioma >= :idioma';

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Asigna los parámetros
    $stmt->bindParam(':id_miembro', $id_miembro, PDO::PARAM_INT);
    $stmt->bindParam(':idioma', $idioma, PDO::PARAM_STR);

    // Realiza la consulta, si hay error lo muestra
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; $Consulta => ' . $query);
    }

    // Si no devuelve ningún resultado, crea el array
    if (!($miembroIdioma = $stmt->fetch(PDO::FETCH_ASSOC))) {
        $miembroIdioma = array(
            'puesto'     => '', 'afiliacion'      => '',
            'curriculum' => '', 'link_curriculum' => ''
        );
    }

    // Une los dos arrays
    $miembro = array_merge($miembro, $miembroIdioma);


    return $miembro;
}


/**
 *
 * @param <type> $id_miembro
 * @return <type> 
 */
function getMiembroBibtex($id_miembro)
{
    // Se crea la consulta
    $query = 'SELECT id_bibtex, texto_bibtex
              FROM miembro_bibtex
              WHERE id_miembro = :id_miembro';

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Asigna los parámetros
    $stmt->bindParam(':id_miembro', $id_miembro, PDO::PARAM_INT);

    // Ejecuta la consulta, si hay error lo muestra
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    $bibtex = array();
    // Obtiene todos los identificadores y textos asociados al miembro
    while ($_bibtex = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bibtex[] = $_bibtex;
    }

   
    return $bibtex;
}
