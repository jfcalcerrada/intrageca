<?php
// Inicializamos el archivo con el script
include('common/init.php');

include('common/proyectos.php');


/**
 * @name proyecto_ver_ficha.php
 *
 * @desc Pagina que genera la ficha del proyecto solicidado. Para ello necesita que
 * sea introducido por parámetro el identificador del proyecto, id_proyecto.
 * @access Público: excepto proyecto no público, privado.
 * @param idp Identificador del Proyecto
 *
 */


// definicion de globales 
// $mbr_rel_grupos;      -- tipo de miembros
// $proy_tipos_monedas   -- tipos de monedas
// $proy_estado_proyecto -- tipos de estado del proyecto


// Obtiene el id_proyecto y verifica si es correcto
$id_proyecto = validar_id($_GET['idp']);


// Muestra el submenú si es el administrador o responsable del proyecto
$contenido = menu_proyectos($contenido, $id_proyecto);


/* CONSULTA LOS DATOS DEL PROYECTO */
// Si es un invitado solo puede ver los proyectos publicos, es decir = 1
$publico = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

// Crea la consulta del proyecto
$consulta_proyecto =
  "SELECT titulo, estado, fecha_inicio, fecha_fin, financiador, importe, ".
    "link_proyecto, descripcion, id_pr_bibtex, moneda, publicar_importe, ".
    "num_referencia ".
  "FROM proyectos LEFT JOIN proyecto_idiomas ".
    "ON proyectos.id_proyecto = proyecto_idiomas.id_proyecto ".
  "WHERE proyectos.id_proyecto = '$id_proyecto' ".
    "AND idioma = '$idioma' ".
    "AND publico >= $publico";

// Realizamos la consulta y comprobamos que no da errores
$resultado_proyecto = mysql_query($consulta_proyecto)
    or error($errors['consulta'], "Error en la consulta: $consulta_proyecto");

// Comprobamos si el miembro existe, es decir, produce resultado
if (mysql_num_rows($resultado_proyecto) == 0)
    error($errors['proyecto'], "El proyecto no existe o invitado, identificador: $id_proyecto");

// Obtiene datos del proyecto
$proyecto = mysql_fetch_array($resultado_proyecto);


/* MUESTRA LOS DATOS DEL PROYECTO */
// Muestra los datos del financiador si existen
if (strlen($proyecto['financiador']) > 0) {
    // Imprime los datos de la empresa financiadora
    $contenido->assign('FINANCIA', $proyecto['financiador']);

    // NOTA! mostar importe solo para la publica o ambas??
    // Muestra, si procede, el importe de la financiacion
    if (($proyecto['publicar_importe'] || $_SESSION['privilegios'] != INVITADO)
            && $proyecto['importe'] > 0) {

        // Introduce la cantidad
        $contenido->assign('VALOR_FINANCIA', number_format($proyecto['importe'], 2, ',', '.'));

        // Introduce la moneda
        $contenido->assign('VALOR_MONEDA', $proy_tipos_monedas[$proyecto['moneda']]);

        // Parsea el importe de la financiacion
        $contenido->parse('content.datos_proyecto.financiacion.importe');
    }

    // Parsea los datos de la financiacion
    $contenido->parse('content.datos_proyecto.financiacion');
}

// NOTA! implementar correctamente esta parte
// Verifica si estado es EN CURSO o TERMINADO
$estado = $proy_estado_proyecto[$proyecto['estado']];

// Verifica si hay que poner fecha de fin
if ($proyecto['estado'] == 1) {
    $contenido->assign('FECHA_FIN', $proyecto['fecha_fin']);
    $contenido->parse('content.datos_proyecto.fecha_fin');
}

// Prepara el array para el parser
$proyecto_datos = array_change_key_case($proyecto, CASE_UPPER);

// Añade el estado del proyecto
$proyecto_datos['ESTADO'] = $proy_estado_proyecto[$proyecto['estado']];

// Contruye el enlace a las publicaciones
$proyecto_datos['DOCUMENTOS'] = 
    "campo1=OPTproyecto&valor1={$proyecto['id_pr_bibtex']}";

// Introduce e imprime los valores en página
$contenido->assign('DATOS', $proyecto_datos);
$contenido->parse('content.datos_proyecto');


/* CONSULTA DE LOS MIEMBROS DEL PROYECTO */
// Consuta los miembros asociados al proyecto
$consulta_gen =
  "SELECT miembros.id_miembro, miembros.nombre, apellidos, responsable, ".
    "investigador_principal ".
  "FROM proyecto_miembros LEFT JOIN miembros ".
    "ON proyecto_miembros.id_miembro = miembros.id_miembro ".
  "WHERE id_proyecto = $id_proyecto ".
    "AND categoria = ";

$orden = "ORDER BY investigador_principal DESC, responsable DESC, apellidos ASC";

// Para cada una de las categorias busca los miembros
foreach ($mbr_rel_grupos as $grupo => $grupo_web) {
    // Construye consulta
    $consulta_miembros = "$consulta_gen '$grupo' $orden";

    // Realizamos la consulta y comprobamos que no da errores
    $resultado_miembros = mysql_query($consulta_miembros)
        or error($errors['consulta'], "Error en la consulta: $consulta_miembros");

    // Comprueba si hay miembros en el proyecto
    if (mysql_num_rows($resultado_miembros) > 0) {

        // Muestra cada uno de los miembros
        while ($miembro = mysql_fetch_array($resultado_miembros)) {

            // Si es el responsable o administrador principal lo mostramos
            if ($miembro['investigador_principal'] == 1) {
                $contenido->parse('content.miembros.miembro.investigador_principal');

            } elseif ($miembro['responsable'] == 1) {
                $contenido->parse('content.miembros.miembro.responsable');
            }

            // Preparamos el array para el template
            $miembro = array_change_key_case($miembro, CASE_UPPER);

            // Añadimos la categoria
            $miembro['CATEGORIA'] = $grupo_web;

            // Asigna los valores y los imprime
            $contenido->assign('MIEMBRO', $miembro);
            $contenido->parse('content.miembros.miembro');

        }
    }
}

// Umprime los miembros
$contenido->parse('content.miembros');


/*
 * CONSULTA DE LOS COLABORADORES DEL PROYECTO
 */
// Consulta los datos de los colaboradores
//$consulta_colaboradores =
//    "SELECT nombre, link_colaborador, nombre_grupo, link_grupo ".
//    "FROM colaboradores LEFT JOIN grupos_colaboradores ".
//        "ON colaboradores.grupo_pertenece = grupos_colaboradores.id_grupo ".
//    "WHERE id_colaborador IN (".
//        "SELECT id_colaborador ".
//        "FROM colaborador_proyectos ".
//        "WHERE id_proyecto = $id_proyecto".
//    ") ".
//    "ORDER BY nombre_grupo ASC";
//
//// Realizamos la consulta y comprobamos que no da errores
//$resultado_colaboradores = mysql_query($consulta_colaboradores)
//    or error($errors['consulta'],
//        "Error en la consulta: $consulta_colaboradores");
//
//// Asignamos los colaboradores
//while ($colaborador = mysql_fetch_array($resultado_colaboradores)) {
//    // Creamos el array del template
//    $colaborador = array_change_key_case($colaborador, CASE_UPPER);
//
//    // Lo asigna y lo imprime
//    $contenido->assign('COLABORADOR', $colaborador);
//    $contenido->parse('content.colaboradores.colaborador');
//}
//
//// Imprime los colaboradores si ha habido alguno
//if (mysql_num_rows($resultado_colaboradores) > 0)
//    $contenido->parse('content.colaboradores');


/*
 * CONSULTA DE LOS SOFTWARE RELACIONADOS CON EL PROYECTO
 */
// Consulta los datos de los software relacionados con el proyecto
//$consulta_software =
//    "SELECT software_idiomas.id_software, titulo,descrip_corta ".
//    "FROM software_idiomas LEFT JOIN software_proyectos ".
//        "ON software_idiomas.id_software = software_proyectos.id_software ".
//    "WHERE idioma = '$idioma' ".
//        "AND id_proyecto = $id_proyecto";
//
//// Realizamos la consulta y comprobamos que no da errores
//$resultado_software = mysql_query($consulta_software)
//    or error($errors['consulta'], "Error en la consulta: $consulta_software");
//
//// Recorremos los diferentes software
//while ($software = mysql_fetch_array($resultado_software)) {
//    // Preparamos el array para el templace
//    $software = array_change_key_case($software, CASE_UPPER);
//
//    // Asignalo a la página
//    $contenido->assign('SOFTWARE', $software);
//    $contenido->parse('content.softwares.software');
//}
//
//// Imprime si hay alguno presente
//if (mysql_num_rows($resultado_software) > 0)
//    $contenido->parse('content.softwares');

// Cierra la conexion con mysql
mysql_close($conexion);


/*
 * MUESTRA LA PAGINA
 */
// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>
