<?php

/**
 *
 */


function accessMember()
{
    if ($_SESSION['privilegios'] == INVITADO) {
        error();
    }
}


function accessOwnMember($id_miembro)
{
    if (!($_SESSION['id_miembro'] == $id_miembro
        || $_SESSION['privilegios'] == ADMIN))
    {
        error();
    }
}


?>
