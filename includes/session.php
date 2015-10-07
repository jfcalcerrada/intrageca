<?php

/**
 *
 */


// Controla el tiempo de la expiraci�n de la session
if (isset($_SESSION['id_miembro'])) {
    sessionTime();
}

// Si no hay sesi�n creada a�n, cargamos los datos por defecto
if (!isset($_SESSION['privilegios'])) {
    $_SESSION['privilegios'] = INVITADO;
}


// Cambiar rol usuario/visitante, si se solicita
if (isset($_GET['rol'])) {

    // Si no esta invitado, le ponemos como invitado y salvamos privilegios
    if ($_SESSION['privilegios'] != INVITADO) {
        // Guardamos los privilegios y le ponesmo como invitado
        $_SESSION['privilegios_old'] = $_SESSION['privilegios'];
        $_SESSION['privilegios']     = INVITADO;

    
    } else {
        // Si quiere volver, restauramos sus privilegios
        $_SESSION['privilegios'] = $_SESSION['privilegios_old'];
    }
}


/**
 * Funcion que controla el tiempo m�ximo de la sessi�n en la intranet
 *
 * @return none
 */
function sessionTime()
{
    // Comprueba si no ha excedido el tiempo y lo actualiza
    if (!isset($_SESSION['last_access'])
      || (time()-$_SESSION['last_access']) <= TIEMPO_SESSION
    ) {
        $_SESSION['last_access'] = time();

    
    } else {
        // Si lo ha excedido termina la sesion
        session_unset();
    }
}

?>