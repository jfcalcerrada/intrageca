<?php

/**
 *
 */


/**
 * Funcion que a partir de un array con los datos del miembro los actualiza en
 * la base de datos. Si el identificado es 0, en vez de actualizar insertar los
 * datos. Si no existen datos para el idioma, también inserta en vez de 
 * actualiar los dependiendes del idioma.
 *
 * @param array $miembro Datos del Miembro
 *
 * @return integer Identificador del Miembro
 */
function setMiembroDatos(array $miembro)
{

    // Si el Identificador de Miembro es 0
    if ($miembro['id_miembro'] == 0) {
        // Crea la consulta de insercion de datos independientes del idioma
        $query = 'INSERT INTO miembros(nombre, apellidos, categoria, activo, 
                      direccion, telefono, fax, email, fecha_incorporacion)
                  VALUES (:nombre, :apellidos, :categoria, :activo,
                      :direccion, :telefono, :fax, :email,
                      :fecha_incorporacion) ';

    
    } else {
        // Crea la consulta de actualizacion de datos independientes del idioma
        $query = 'UPDATE miembros 
                  SET nombre = :nombre,        apellidos = :apellidos,
                      categoria = :categoria,  activo = :activo,
                      direccion = :direccion,  telefono = :telefono,
                      fax = :fax,              email = :email,
                      fecha_incorporacion = :fecha_incorporacion
                  WHERE id_miembro = :id_miembro';
    }

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);
    

    // Actualiza los datos
    if (!$stmt->execute(arraySql($miembro, $query))) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    if ($miembro['id_miembro'] == 0) {
        $miembro['id_miembro'] = DB::singleton()->lastInsertId();
    }


    // Comprueba si existen los datos del idioma para saber si INSERT o UPDATE
    $query = 'SELECT id_miembro 
              FROM miembro_idiomas
              WHERE id_miembro = :id_miembro
                  AND idioma = :idioma ';

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Realiza la consulta
    if (!$stmt->execute(arraySql($miembro, $query))) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    // Si hay resultados, crea la query de UPDATE
    if ($stmt->rowCount() != 0) {
        $query = 'UPDATE miembro_idiomas 
                  SET puesto = :puesto,
                      afiliacion = :afiliacion,
                      curriculum = :curriculum,
                      link_curriculum = :link_curriculum
                  WHERE id_miembro = :id_miembro
                      AND idioma = :idioma';

    } else {
        // Si no hay crea la query de INSERT
        $query = 'INSERT INTO miembro_idiomas (id_miembro, idioma, puesto, 
                      afiliacion, curriculum, link_curriculum)
                  VALUES (:id_miembro, :idioma, :puesto,
                      :afiliacion, :curriculum, :link_curriculum)';
    }

    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Realiza la consulta, si hay error lo muestra
    if (!$stmt->execute(arraySql($miembro, $query))) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    return $miembro['id_miembro'];
}


?>
