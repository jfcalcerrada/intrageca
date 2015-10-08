<?php

require_once 'common/init.php';

require_once 'common/proyectos.php';


//--------------------------------------------------------------------------
// proyecto_editar_mie.php
//
// Genera el formulario de relaciones del proyecto en cuestion. Para ello
// consulta las tablas de relacion con miembros.
//
// Parametros de entrada
//   idp : Identidad del proyecto
//--------------------------------------------------------------------------


// Obtiene el id_miembro y verifica si es correcto
$id_proyecto = (isset($_POST['id_proyecto']))
    ? validar_id($_POST['id_proyecto']) : validar_id($_GET['idp']);

// Controla el acceso a la pagina
acceso_proyecto($id_proyecto);

// Realiza la actualizacion de los datos
$actualizar = (isset($_POST['actualizar']) && $_POST['actualizar'] == 1) ? true : false;


// Muestra el submenú, y si es el administrador el botón de borrar
$contenido = menu_proyectos($contenido, $id_proyecto);


/* INSERT LOS ELEMENTOS NUEVOS */
// Si se ha actualiza y se ha insertado algun miembro
if ($actualizar && isset($_POST['nuevo_miembro']) && strlen($_POST['nuevo_miembro']) > 0) {
    // Recoge el valor del campo nuevo
    $inserta_miembro =
      "INSERT INTO proyecto_miembros (id_proyecto, id_miembro) ".
      "VALUES ('$id_proyecto', '{$_POST['nuevo_miembro']}')";

    // Inserta la nueva refencia
    mysql_query($inserta_miembro)
      or error($errors['actualizar'], "Error en la consulta: $inserta_miembro");

}


/* ACTULIZA/BORRA LOS CAMPOS MARCADOS */
// Verificamos si se ha actualizado
if ($actualizar) {

  // Recorremos todos los registros
  for ($i = 1; $i < $_POST['numero']; $i++) {

    // Guardamos el miembro
    $id_miembro = $_POST["id_miembro_$i"];

    // Comprueba si esta marcado para borrar
    if ($_POST["borrar_$i"] && strlen($_POST["id_miembro_$i"]) > 0) {
      // Prepara el borrado
      $borra_miembro =
        "DELETE FROM proyecto_miembros ".
        "WHERE id_proyecto = $id_proyecto ".
          "AND id_miembro = $id_miembro";

      // Realiza el borrado
      mysql_query($borra_miembro)
        or error($errors['actualiza'], "Error en la consulta: $borra_miembro");

    // @nota: mejora, mirar si hay diferencias entre checked's
    // Actualizamos los registros de responsable e investigador
    } else {
      // Guardamos los datos
      $responsable = (isset($_POST["responsable_$i"])) ? $_POST["responsable_$i"] : '0';
      $investigador_ppal = (isset($_POST["principal_$i"])) ? $_POST["principal_$i"] : '0';
            
      // Prepara la actualización
      $actualiza_miembro =
        "UPDATE proyecto_miembros ".
        "SET responsable = '$responsable', ".
          "investigador_principal = $investigador_ppal ".
        "WHERE id_proyecto = '$id_proyecto' ".
          "AND id_miembro = '$id_miembro'";
            
      // Realiza la actualizacion
      mysql_query($actualiza_miembro)
        or error($errors['actualiza'], "Error en la consulta: $actualiza_miembro");
    }
  }
}


/* MUESTRA LOS MIEMBROS DEL PROYECTO */
// Consulta los miembros del proyecto y su nombre
$consulta_miembros =
  "SELECT miembros.id_miembro, nombre, apellidos, responsable, investigador_principal ".
  "FROM proyecto_miembros LEFT JOIN miembros ".
    "ON proyecto_miembros.id_miembro = miembros.id_miembro ".
  "WHERE id_proyecto = '$id_proyecto'".
  "ORDER BY investigador_principal DESC, responsable DESC, apellidos ASC";

// Realiza la consulta
$resultado_miembros = mysql_query($consulta_miembros)
  or error($errors['consulta'],"Error en la consulta: $consulta_miembros");

// Numero de miembros del proyecto
$indice = 1;

// Recorremos los miembros del proyecto
while ($miembro = mysql_fetch_array($resultado_miembros)) {

    // Lo marcamos si es responsalbe
    $miembro['responsable'] = ($miembro['responsable'] == 1) ? 'checked="checked"' : '';
    // Lo marcamos si es investigador principal
    $miembro['investigador_principal'] = ($miembro['investigador_principal'] == 1) ? 'checked="checked"' : '';

    // Insertamos el indice
    $miembro['indice'] = $indice++;
    // Preparamos el array para insertarlo
    $miembro = array_change_key_case($miembro, CASE_UPPER);

    //Asigna y parse
    $contenido->assign('MIEMBRO', $miembro);
    $contenido->parse('content.miembros.miembro');
}


/* LISTA DE LOS POSIBLES MIEMBROS A ELEGIR */
$consulta_miembros =
  "SELECT id_miembro, nombre, apellidos ".
  "FROM miembros ";//.
  "WHERE id_miembro NOT IN
    (SELECT id_miembro
    FROM proyecto_miembros
    WHERE id_proyecto = '$id_proyecto') ".
  "ORDER BY apellidos ASC";

// Realizamos la consulta y comprobamos que no da errores
$resultado_miembros = mysql_query($consulta_miembros)
  or error($errors['consulta'], "Error en la consulta: $consulta_miembros");

// Lista de miembros seleccionables
while ($miembro = mysql_fetch_array($resultado_miembros)) {
  // Preparamos el array para insertarlo
  $miembro = array_change_key_case($miembro, CASE_UPPER);

  //Asigna y parse
  $contenido->assign('MIEMBRO', $miembro);
  $contenido->parse('content.miembros.select_miembro');
}


// Asigna el identificador y el numero
$contenido->assign('ID_PROYECTO', $id_proyecto);
$contenido->assign('NUMERO_MIEMBROS', $indice);

// Parse las referencias
$contenido->parse('content.miembros');


// Cierra la conexion con mysql
mysql_close($conexion);


/* MUESTRA LA PAGINA */
// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>