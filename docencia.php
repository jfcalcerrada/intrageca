<?php
// Inicializamos el archivo con el script
include('common/init.php');


/**
 * docencia.php
 *
 * Genera una pagina con el listado de asignaturas impartidas por el grupo. Cada
 * asignatura tiene un enlace a una web, que por defecto sera a la web de TSC al
 * apartado de docencia.
 *
 * Si se recibe por parametro un identificador de miembro, idm, solo mostrara
 * las asignaturas que imparta dicho miembro
 *
 */


/* OBTIENE EL PARAMETRO IDM SI EXISTE */
// Parametro que sirver para buscar las asignaturas de un profesor en concreto
if (isset($_GET['idm']))
  $id_miembro = validar_id($_GET['idm']);


/* CONSULTA DE TODAS LAS ASIGNATURAS */
// Si es un invitado solo puede ver las asignaturas publicos, es decir 1
$publico = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

// Consulta de los proyectos
$consulta_asignaturas =
  "SELECT asignaturas.id_asignatura, nombre, link ".
  "FROM asignaturas LEFT JOIN asignatura_idiomas ".
    "ON asignaturas.id_asignatura = asignatura_idiomas.id_asignatura ".
  "WHERE idioma = '$idioma' ".
    "AND publico >= $publico ";

// Si se ha introducido un identificador de miembro, realizamos una prebusqueda
if (isset($id_miembro))
  $consulta_asignaturas .=
    "AND asignaturas.id_asignatura IN ".
    "(SELECT id_asignatura ".
    "FROM asignatura_miembros ".
    "WHERE id_miembro = $id_miembro) ";


// Ordenamos alfabeticamente
$consulta_asignaturas .= "ORDER BY nombre ASC";

// Realizamos la consulta y comprobamos que no da errores
$resultado_asignaturas = mysql_query($consulta_asignaturas)
  or error($errors['consulta'], "Error en la consulta: $consulta_asignaturas");


// Imprime para cada asignatura
while ($asignatura = mysql_fetch_array($resultado_asignaturas)) {

  // Prepara el array a parsear
  $asignatura = array_change_key_case($asignatura, CASE_UPPER);

  // Imprime el proyecto
  $contenido->assign('ASIGNATURA', $asignatura);


  // Si es el administrador, damos acceso a la edicion de asignaturas
  if ($_SESSION['privilegios'] == ADMIN)
    $contenido->parse('content.asignaturas.asignatura.editar');


  /* CONSULTA DE LOS MIEMBROS ASOCIADOS A LA ASIGNATURA */
  // Crea la consulta
  $consulta_miembros =
    "SELECT miembros.id_miembro, miembros.nombre, apellidos, coordinador ".
    "FROM miembros LEFT JOIN asignatura_miembros ".
      "ON asignatura_miembros.id_miembro = miembros.id_miembro ".
    "WHERE asignatura_miembros.id_asignatura = {$asignatura['ID_ASIGNATURA']} ".
      "AND miembros.activo >= $publico ".
    "ORDER BY coordinador DESC, miembros.apellidos ASC";

  // Realizamos la consulta y comprobamos que no da errores
  $resultado_miembros = mysql_query($consulta_miembros)
      or error($errors['consulta'], "Error en la consulta: $consulta_miembros");

  // Comprueba si hay miembros en el proyecto
  if (mysql_num_rows($resultado_miembros) > 0) {

    // Flag de coordinador
    $coordinador = false;

    // Muestra cada uno de los miembros
    while ($miembro = mysql_fetch_array($resultado_miembros)) {

      // Preparamos el array para el template
      $miembro = array_change_key_case($miembro, CASE_UPPER);

      // Asigna los valores y los imprime
      $contenido->assign('MIEMBRO', $miembro);

      // Comprueba si se trata de un un coordinador
      if ($miembro['COORDINADOR'] == 1) {
        // Aumenta el flag
        $coordinador = true;
        // Imprime el miembro coordinador
        $contenido->parse('content.asignaturas.asignatura.coordinador.miembro');

      // Si se trata de un miembro
      } else {
        // Y se ha imprimido algún coordinador, imprime el bloque coordinador
        if ($coordinador == true) {
          $coordinador = false;
          $contenido->parse('content.asignaturas.asignatura.coordinador');
        }

        // Imprime el miembro que imparte la asignatura
        $contenido->parse('content.asignaturas.asignatura.miembros.miembro');
      }
    }

    // Añandimos los miembros
    $contenido->parse('content.asignaturas.asignatura.miembros');
  }


  // Lo imprimimos
  $contenido->parse('content.asignaturas.asignatura');
}

// Cierra y muestra las asignaturas
$contenido->parse('content.asignaturas');

// Cierra la conexion con mysql
mysql_close($conexion);


/* BOTON AÑADIR PROYECTOS SOLO ADMIN */
// Mostramos el boton de añadir proyecto si es el administrador
if($_SESSION['privilegios'] == ADMIN)
  $contenido->parse('content.anyadir');


/* MUESTRA LA PAGINA */
// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>