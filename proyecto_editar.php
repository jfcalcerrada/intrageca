<?php
// Inicializamos el archivo con el script
include('common/init.php');

include('common/proyectos.php');
include("proyecto_insertar.php");


/**
 * proyecto_editar.php
 *
 * Genera el formulario con los datos del proyecto para poder modificarlos. Para
 * ello usa como parametro el identificador de miembro. El acceso esta permitido
 * solo a los administradores y a los responsables del proyecto.
 *
 * @param idp : Identificador del Proyecto
 */

// definicion de globales 
// $proy_tipos_monedas; -- tipos de monedas usadas
// $proy_estado_proyecto -- estados del proyecto
// $pry_proyecto_desc -- mensaje de proyecto desconocido
// $gen_idiomas_disp -- idiomas disponibles


// Obtiene el id_miembro y verifica si es correcto
$id_proyecto = (isset($_POST['id_proyecto']))
    ? validar_id($_POST['id_proyecto']) : validar_id($_GET['idp']);

// Controla el acceso a la pagina
acceso_proyecto($id_proyecto);

// Realiza la actualizacion de los datos
if (isset($_POST['actualizar']) && $_POST['actualizar'] == 1)
  $id_proyecto = proyecto_insertar($_POST);


// Seleccionamos el idioma en que se esta mostrando el
if (isset($_POST['idioma_cambio']) && strlen($_POST['idioma_cambio'])
  && array_key_exists($_POST['idioma_cambio'], $gen_idiomas_disp)) {
  $proyecto_idioma = $_POST['idioma_cambio'];
} else {
  $proyecto_idioma = $idioma;
}

// Muestra el submenú, y si es el administrador el botón de borrar
$contenido = menu_proyectos($contenido, $id_proyecto);


/* CONSULTA DE DATOS DEL PROYECTO */
// Si el identificador de proyecto es distinto de 0 muestra el proyecto
if ($id_proyecto > 0) {
// Consulta sobre los datos del proyecto
  $consulta_proyecto =
    "SELECT proyectos.id_proyecto, titulo, descripcion, descrip_corta, ".
      "publico, estado, fecha_inicio, fecha_fin, financiador, importe, ".
      "moneda, publicar_importe, link_proyecto, id_pr_bibtex, num_referencia ".
    "FROM proyectos LEFT JOIN proyecto_idiomas ".
      "ON proyectos.id_proyecto = proyecto_idiomas.id_proyecto ".
    "WHERE proyectos.id_proyecto = '$id_proyecto' ".
      "AND idioma = '$proyecto_idioma'";

  // Realizamos la consulta y comprobamos que no da errores
  $resultado_proyecto = mysql_query($consulta_proyecto)
    or error($errors['consulta'], "Error en la consulta: $consulta_proyecto");

  // Chequea si existe dicho proyecto, deberia existir puesto que ha accedido
  if (mysql_num_rows($resultado_proyecto) == 0) {

  // Consulta sobre los datos del proyecto sin idioma
    $consulta_proyecto =
      "SELECT id_proyecto, publico, estado, fecha_inicio, fecha_fin, ".
        "financiador, importe, moneda, link_proyecto, id_pr_bibtex, ".
        "publicar_importe, num_referencia ".
      "FROM proyectos ".
      "WHERE id_proyecto = '$id_proyecto'";

    // Realizamos la consulta y comprobamos que no da errores
    $resultado_proyecto = mysql_query($consulta_proyecto)
      or error($errors['consulta'], "Error en la consulta: $consulta_proyecto");

    // Si no existe el proyecto ni sin idiomas
    if (mysql_num_rows($resultado_proyecto) == 0) {
      error($errors['proyecto'], 'Problema en el archivo proyecto editar');
    }
  }

  // Obtiene los datos del proyecto
  $proyecto = mysql_fetch_array($resultado_proyecto);


  /* PREPARA EL ARRAY PARA EL TEMPLATE */
  // Marca como checked si es de tipo público
  $proyecto['publico'] = ($proyecto['publico'] == 1) ? 'checked="checked"' : '';


// Si el identificador es 0 se esta insertando un nuevo proyecto
} elseif ($id_proyecto == 0) {
// Rellena solo el valor de IDM para autollamada de formulario en blanco
  $proyecto = array('id_proyecto' => 0);
}

// Cierra la conexion con mysql
mysql_close($conexion);


/* MUESTRA LOS VALORES EN LA PÁGINA */
// Asigna el campo oculto con el codigo actual, en el que se muestran los datos
$contenido->assign('COD_IDIOMA', $proyecto_idioma);

// Imprime los idiomas disponibles
foreach($gen_idiomas_disp as $clave_idioma => $texto_idioma) {
// Selecciona el idioma que se esta editanto
  $selected = ($proyecto_idioma == $clave_idioma) ? 'selected="selected"': '';

  // Asigna la lista
  $idioma_lista = array (
    'CLAVE'        => $clave_idioma,
    'TEXTO'        => $texto_idioma,
    'SELECCIONADO' => $selected);

  // insertalo en página
  $contenido->assign('IDIOMA', $idioma_lista);
  $contenido->parse('content.ficha.idioma');
}

// Asigna estado a select box de estado
foreach ($proy_estado_proyecto as $clave_estado => $texto_estado) {
// Selecciona el estado del proyecto
  $selected = ($proyecto['estado'] == $clave_estado) ? 'selected="selected"' :  '';

  // Selecciona elementos
  $estado = array(
    'CLAVE'        => $clave_estado,
    'TEXTO'        => $texto_estado,
    'SELECCIONADO' => $selected);

  // Asigna a la página el elemento de la lista
  $contenido->assign('ESTADO', $estado);
  $contenido->parse('content.ficha.estado');
}


// Extrae los valores de la fecha de inicio del proyecto
$proyecto['anyo_inicio'] = strtok($proyecto['fecha_inicio'], '-');
$proyecto['mes_inicio']= strtok('-');
$proyecto['dia_inicio'] = strtok('-');

// Extrae los valores de la fecha de fin del proyecto
$proyecto['anyo_fin'] = strtok($proyecto['fecha_fin'], '-');
$proyecto['mes_fin']= strtok('-');
$proyecto['dia_fin'] = strtok('-');

// asigna monedas
foreach ($proy_tipos_monedas as $clave_moneda=> $texto_moneda) {

  $selected = ($proyecto['moneda'] == $clave_moneda) ? 'selected="selected"' : '';

  $moneda = array(
    'CLAVE'     => $clave_moneda,
    'NOMBRE'    => $texto_moneda,
    'SELECCION' => $selected);

  $contenido->assign('MONEDA', $moneda);

  $contenido->parse('content.ficha.select_moneda');
}

// Publicar importe en la Web pública
$proyecto['publicar_importe'] = ($proyecto['publicar_importe'] == 1) ? 'checked="checked"' : '';


// Preparamos el array para parsearlo
$proyecto = array_change_key_case($proyecto, CASE_UPPER);

// Imprime los datos del proyecto
$contenido->assign('PROYECTO', $proyecto);
$contenido->parse('content.ficha');


/* MUESTRA LA PAGINA */
// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>