<?

//--------------------------------------------------------------------------
// proyecto_insertar.php
//
// Esta funcion actualiza los valores de un proyecto
// en la base de datos.
// 
// Los parámetros de entrada de la función son:
//  $conexion : El manejador de la conexion a la base de datos
//  $registro : Array con los valores a actualizar. Estos son:
//   - id_proyecto : Identificador de proyecto. Si es 0, el registro es nuevo.
//   - titulo : el titulo del proyecto
//   - publico : indica si el proyecto esta publicado
//   - estado : estado del proyecto
//   - dia_ini, mes_ini, anyo_ini : fecha de inicio
//   - dia_fin, mes_fin, anyo_fin : fecha de fin
//   - entidad:  entidad financiera
//   - importe: importe de la financiacion
//   - homepage: link a página principal del proyecto
//   - id_bibtex: campo OPT proyecto para referencias
//   - descripcion: textarea de descripcion
//   - desc_corta: descripcion corta
//
//  Devuelve el identificador del registro insertado/actualizado
//--------------------------------------------------------------------------

function proyecto_insertar($registro)
{
    GLOBAL $errors;

    /*
     * PREPARA LOS DATOS A INSERTAR O ACTUALIZAR
     */
    $idioma = $registro['idioma'];

    $titulo = addslashes($registro['titulo']);
    $descrip_corta =  addslashes($registro['descrip_corta']);

    $num_referencia = $registro['num_referencia'];
    $publico = ($registro['publico'] == 1) ? 1 : 0;

    // Comprueba si existe la fecha
    $mes_ini = (isset($registro['mes_ini']) < 0) ? $registro['mes_ini'] : '00';
    $dia_ini = (isset($registro['dia_ini']) < 0) ? $registro['dia_ini'] : '00';
    $anyo_ini = 
        (isset($registro['anyo_ini']) < 0) ? $registro['anyo_ini'] : '0000';

    $mes_fin = (isset($registro['mes_fin']) < 0) ? $registro['mes_fin'] : '00';
    $dia_fin = (isset($registro['dia_fin']) < 0) ? $registro['dia_fin'] : '00';
    $anyo_fin =
        (strlen($registro['anyo_fin']) < 0) ? $registro['anyo_fin'] : '0000';

    // Comprueba si la fecha son válidas
    $fecha_ini = '';
    if (checkdate($mes_ini, $dia_ini, $anyo_ini)) {
        $fecha_ini = "$anyo_ini-$mes_ini-$dia_ini";
    }

    $fecha_fin = '';
    if (checkdate($mes_fin, $dia_fin, $anyo_fin)) {
        $fecha_fin = "$anyo_fin-$mes_fin-$dia_fin";
    }


    // Comprobamos a partir de las fechas el estado del proyecto
    if (!isset($fecha_ini) || $fecha_ini > date('Y-m-d', time())) {
        $estado = 0;
    } elseif ($fecha_ini < $fecha_fin && $fecha_fin < date('Y-m-d', time())){
        $estado = 2;
    } else {
        $estado = 1;
    }

    $financiador =  addslashes($registro['entidad']);
    list($importe) = sscanf($registro['importe'], "%d");
    if (strlen($importe)==0)
        $importe = 0;

    $moneda = $registro['moneda'];
    $publicar_importe = (isset($registro['publicar_importe']) && $registro['publicar_importe'] == 1) ? 1 : 0;

    $homepage = addslashes($registro['homepage']);
    $id_bibtex =  addslashes($registro['id_bibtex']);

    $descripcion =  addslashes($registro['descripcion']);


    /*
     * PROCEDE A LA MODIFICACION DE LOS DATOS NO DEPENDIENTES DEL IDIOMA
     */
    // Si el identificador es igual a 0, estamos insertando un nuevo proyecto
    if ($registro['id_proyecto'] == 0) {
        
        // construye la consulta de inserccion
        $consulta_proyecto =
            "INSERT INTO proyectos(id_pr_bibtex, num_referencia, publico, ".
                "estado, fecha_inicio, fecha_fin, financiador, importe, ".
                "moneda, publicar_importe, link_proyecto) ".
            "VALUES ('$id_bibtex', '$num_referencia', '$publico','$estado', ".
                "'$fecha_ini', '$fecha_fin.', '$financiador', '$importe', ".
                "'$moneda',  '$publicar_importe', '$homepage')";

    // Si tiene id_proyecto es que es una actualizacion
    } else {
        $consulta_proyecto =
            "UPDATE proyectos ".
            "SET id_pr_bibtex = '$id_bibtex', ".
                "num_referencia = '$num_referencia', ".
                "publico = $publico, ".
                "estado = $estado, ".
                "fecha_inicio = '$fecha_ini', ".
                "fecha_fin = '$fecha_fin', ".
                "financiador = '$financiador', ".
                "importe = $importe, ".
                "moneda = '$moneda', ".
                "publicar_importe = $publicar_importe, ".
                "link_proyecto = '$homepage' ".
            "WHERE id_proyecto = {$registro['id_proyecto']}";
    }

    // Realiza la insercion/actualizacion del proyecto en la tabla principal
    mysql_query($consulta_proyecto)
        or error($errors['consulta'], "Error en la consulta: $consulta_proyecto");


    /*
     * PROCEDE A LA MODIFICACION DE LOS DATOS DEPENDIENTES DEL IDIOMA
     */

    // Obtiene el valor del registro insertado/actualizado
    if ($registro['id_proyecto'] == 0) {
        // Obtenemos el identificador
        $id_proyecto = mysql_insert_id();

    } else {
        // Obtenemos el identificador a actualizar
        $id_proyecto = $registro['id_proyecto'];
    }

    // Comprobamos si existe para el idioma para saber si tenemos que
    // insertar o actualizar
    $consulta_idioma =
        "SELECT COUNT(*) AS existe ".
        "FROM proyecto_idiomas ".
        "WHERE id_proyecto = $id_proyecto ".
            "AND idioma = '$idioma'";

    $resultado_idioma = mysql_query($consulta_idioma)
        or error($errors['consulta'],
            "Error en la consulta: $consulta_idioma");

    // Obtenemos el valor para comprobar si existe
    $resultado_idioma = mysql_fetch_array($resultado_idioma);

    // Si no existe para dicho idioma, lo creamos
    if ($resultado_idioma['existe'] == 0) {
        $consulta_proyecto_idioma =
            "INSERT INTO proyecto_idiomas (id_proyecto, idioma, titulo, ".
                "descrip_corta, descripcion) ".
            "VALUES ('$id_proyecto', '$idioma', '$titulo', ".
                "'$descrip_corta', '$descripcion')";

    } else {
        // Actualizacion del idioma
        $consulta_proyecto_idioma =
            "UPDATE proyecto_idiomas ".
            "SET titulo = '$titulo', ".
                "descrip_corta = '$descrip_corta', ".
                "descripcion = '$descripcion' ".
            "WHERE id_proyecto = $id_proyecto ".
                "AND idioma = '$idioma'";
    }

    // Realiza la insercion/actualizacion del proyecto en la tabla idioma
    mysql_query($consulta_proyecto_idioma)
        or error($errors['consulta'],
            "Error en la consulta: $consulta_proyecto_idioma");


    // Devuelve el valor de identidad del proyecto
    return $id_proyecto;

} 

?>