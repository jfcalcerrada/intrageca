<?php

function error($key, $log = '') {
    global $_error;
    global $_lang;

    $error = $_error[$key].'<br />'.$key.': '.$log;

    include 'vista/error.php';

    // Matamos el proceso, para que no siga en caso de error
    die();
}


?>
