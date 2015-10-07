<?php
// Inicializamos el archivo con el script
include ("common/init.php");
include ("autenticacion.php");
// Autenticamos al usuario
autenticar_usuario();

include ("common/common_pub.php");

if(isset($_GET['idm'])) {
    $id_miembro = $_GET['idm'];
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

echo $fila["author"].$fila['title'].$fila['other'];

foreach($campos as $campo => $valor) {

    foreach($public_formatos as $clave => $tipo) {
        $contenido->assign('VALUE', $clave);
        $contenido->assign('MOSTRADO', $tipo['texto']);
        
        if($clave == $valor) {
            $contenido->assign('SELECTED', ' selected="selected"');
        } else {
            $contenido->assign('SELECTED', '');
        }
        $contenido->parse("content.fila.opciones");
    }
    
    $contenido->assign('CAMPO', $campo);
    $contenido->parse("content.fila");

}

$contenido->assign("IDM", $id_miembro);
$contenido->parse("content");

$pagina = new XTemplate ("templates/es/pagina.html");
//$pagina->assign("TITULO", $titulos_web[$archivo]);
$pagina->assign("CONTENIDO", $contenido->text("content"));
$pagina->parse("main");
$pagina->out("main");

?>
