<?php

/**
 *
 */

// Carga los includes de la cabecera
require_once 'common/init.php';

require_once "common/common_pub.php";
require_once 'model/includes/miembros.php';

if(isset($_GET['id_miembro'])) {
    $id_miembro = $_GET['id_miembro'];
} else {
    ERR_muestra_pagina_error($mbr_usuario_desc, "");
    exit;
}

if(isset($_POST['modificado']) && $_POST['modificado'] == 1) {
    $sql = "UPDATE formato_bibtex ".
           "SET author = '".$_POST['author']."', ".
               "title = '".$_POST['title']."', ".
               "other = '".$_POST['other']."' ".
           "WHERE id_miembro = '".$id_miembro."'";

    mysql_query($sql);

    if(mysql_affected_rows == 0) {
        $sql = "INSERT INTO formato_bibtex ".
               "VALUES (".$id_miembro.", 'Predeterminado', ".
                    "'".$_POST['author']."', '".$_POST['title']."', ".
                    "'".$_POST['other']."')";
        mysql_query($sql);
    }

}

$miembro = getMiembroDatos($id_miembro);

// Asigna la variable miembro a la plantilla
$_content->assign('MIEMBRO', arrayUpper($miembro));


// Si es el miembro o si es el ADMIN, carga el submenu
$isAdmin  = ($_SESSION['privilegios'] == ADMIN);

if ($_SESSION['id_miembro'] == $id_miembro || $isAdmin) {
    // Si es Administrador, muestra el menu de borrar
    if ($isAdmin) {
        $_content->parse('content.submenu.borrar');
    }
    $_content->parse('content.submenu');
}


$consulta_formato = "SELECT tipo, author, title, other ".
        "FROM formato_bibtex ".
        "WHERE id_miembro IN (0, ".$id_miembro.") ".
        "ORDER BY id_miembro DESC";

$resultado = mysql_query($consulta_formato);

if(!($fila = mysql_fetch_array($resultado))) {
    echo "Error en la búsqueda";
    exit;
}

$campos = array ( 'author' => $fila['author'],
                  'title' => $fila['title'],
                  'other' => $fila['other']);

foreach($campos as $campo => $valor) {
    // TODO sin implementar
    break;

    foreach($public_formatos as $clave => $tipo) {
        $_content->assign('VALUE', $clave);
        $_content->assign('MOSTRADO', $tipo['texto']);
        
        if($clave == $valor) {
            $_content->assign('SELECTED', ' selected="selected"');
        } else {
            $_content->assign('SELECTED', '');
        }
        $_content->parse("content.fila.opciones");
    }
    
    $_content->assign('CAMPO', $campo);
    $_content->parse("content.fila");

}

$_content->assign("IDM", $id_miembro);
// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';