<?php

/**
 * 
 */


/**
 * Comprueba si el miembro tiene asignaturas asociadas
 *
 * @param array $id_miembro Identificador del miembro
 * 
 * @return boolean Existen asignaturas asociadas
 */
function getMiembroDocencia($id_miembro)
{
    // Se crea la consulta
    $query = 'SELECT id_asignatura 
              FROM asignatura_miembros
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

    return ($stmt->rowCount() != 0);
}

/**
 * Obtiene el enlace para el miembro de sus publicaciones asociadas, a través
 * del buscador
 *
 * @param integer $id_miembro Identificador del Miembro
 *
 * @return string Url a la página del buscador
 */
function getEnlaceMiembroPublicaciones($id_miembro)
{
    // Obtiene los campos bibtex del miembro
    $bibtex = getMiembroBibtex($id_miembro);


    // Comprueba que hay
    if (!isset($bibtex)) {
        return false;
    }


    // Prepara el array de la query
    $htmlQuery = array('logica' => 'OR');

    // Añade los campos author con su valor
    $num_campos = count($bibtex);
    $i = 0;
    while ($i < $num_campos) {
        ++$i;
        $htmlQuery['campo'.$i] = 'author';
        $htmlQuery['valor'.$i] = $bibtex[$i-1]['texto_bibtex'];
    }

    // Agrega el numero total de campos
    $htmlQuery['num_campos'] = $num_campos;

   
    return http_build_query($htmlQuery);
}

