<?php

// Controla el tiempo de la expiración de la session
if (isset($_SESSION['id_miembro'])) {
    sessionTime();
}

// Si no hay sesión creada aún, cargamos los datos por defecto
if (!isset($_SESSION['privilegios'])) {
    $_SESSION['privilegios'] = INVITADO;
}

// Cambiar rol usuario/visitante, si se solicita
if (isset($_GET['rol']) && isset($_SESSION['id_miembro'])) {

    // Si no esta invitado, le ponemos como invitado y salvamos privilegios
    if ($_SESSION['privilegios'] !== INVITADO) {
        // Guardamos los privilegios y le ponesmo como invitado
        $_SESSION['privilegios_old'] = $_SESSION['privilegios'];
        $_SESSION['privilegios']     = INVITADO;

    } else {
        // Si quiere volver, restauramos sus privilegios
        $_SESSION['privilegios'] = $_SESSION['privilegios_old'];
    }
}


/**
 * Funcion que controla el tiempo máximo de la sessión en la intranet
 *
 * @return void
 */
function sessionTime()
{
    if (!isset($_SESSION['last_access']) || (time() - $_SESSION['last_access']) <= TIEMPO_SESSION) {
        $_SESSION['last_access'] = time();

    } else {
        $_SESSION['id_miembro']  = null;
        $_SESSION['privilegios'] = null;

        unset($_SESSION['id_miembro']);
        unset($_SESSION['privilegios']);
    }
}



function autenticar_usuario()
{
    if (isset($_SESSION['id_miembro'])) {
        return;
    }

    header('Location: ' . url('login.php'));
    die();
}