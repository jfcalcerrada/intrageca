<?php

/**
 * Controla el acceso a paginas en las que solo esta permitido el acceso de
 * miembros en general o el administrador
 */
function acceso_miembros($redireccion) 
{
    if ($_SESSION['privilegios'] == INVITADO)
        error($errors['privilegios'], 'No tiene privilegios para acceder', $redireccion);
}

?>
