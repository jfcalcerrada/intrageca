<?php

/**
 *
 */

/**
 *
 */
function deleteBibtex($id_bibtex, $id_miembro)
{
    // Crea la consulta
    $query = 'DELETE FROM miembro_bibtex
              WHERE id_bibtex = :id_bibtex
                  AND id_miembro = :id_miembro';


    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);


    // Asigna los parámetros
    $stmt->bindParam(':id_bibtex', $id_bibtex, PDO::PARAM_INT);
    $stmt->bindParam(':id_miembro', $id_miembro, PDO::PARAM_INT);

        // Ejecuta la consulta, si hay error lo muestra
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    return true;
}

/**
 *
 */
function updateBibtex($id_bibtex, $id_miembro, $texto_bibtex) {
    // Crea la consulta
    $query = 'UPDATE miembro_bibtex
              SET texto_bibtex = :texto_bibtex
              WHERE id_bibtex = :id_bibtex
                  AND id_miembro = :id_miembro';


    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);


    // Asigna los parámetros
    $stmt->bindParam(':id_bibtex', $id_bibtex, PDO::PARAM_INT);
    $stmt->bindParam(':texto_bibtex', $texto_bibtex, PDO::PARAM_STR);
    $stmt->bindParam(':id_miembro', $id_miembro, PDO::PARAM_INT);

    // Ejecuta la consulta, si hay error lo muestra
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    return true;
}


/**
 *
 */
function insertBibtex($id_miembro, $texto_bibtex) {
    // Crea la consulta
    $query = 'INSERT INTO miembro_bibtex
              VALUES ("", :id_miembro, :texto_bibtex)';


    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);


    // Asigna los parámetros
    $stmt->bindParam(':id_miembro', $id_miembro, PDO::PARAM_INT);
    $stmt->bindParam(':texto_bibtex', $texto_bibtex, PDO::PARAM_STR);


        // Ejecuta la consulta, si hay error lo muestra
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    return true;
}


?>
