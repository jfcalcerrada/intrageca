<?php

/**
 * Comprueba si existen el usuario y si la clave es correcta, en caso afirmativo
 * obtiene los datos de la sesion
 *
 * @param string $usuario  Usuario
 * @param string $password Password
 *
 * @return array Los parámetros de la sesión
 */
function getMiembroAutentica($usuario, $password)
{
    // Se crea la consulta
    $query = 'SELECT miembros.id_miembro, nombre, privilegios
              FROM miembros LEFT JOIN miembro_autentica
                  ON miembros.id_miembro = miembro_autentica.id_miembro
              WHERE usuario_web = :usuario
                  AND password_web = MD5(:password)';


    // Prepara la consulta
    $stmt = DB::singleton()->prepare($query);

    // Asigna los parámetros
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    // Escapa los caracteres especiales
    $password = addslashes($password);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);


    // Realiza la consulta, si hay error lo muestra
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        error('consulta', $error[2] . '; Consulta => ' . $query);
    }

    // Si no hay resultados, muestra error
    if (!($session = $stmt->fetch(PDO::FETCH_ASSOC))) {
        error('usuario', 'No existe el usuario ' . $usuario . ' @ ' . $password);
    }


    return $session;
}
