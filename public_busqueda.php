<?php
// Inicializamos el archivo con el script
include('common/init.php');

include('common/common_pub.php');


$id_miembro = $_SESSION['id_usuario'];
//$public_marcacion_campos = cargar_formatos($public_marcacion_campos, $id_miembro, $conexion);


// Obtenemos los argumentos de la busqueda
$argumentos = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "?"));

// NOTA: comentar
$flag_busqueda_vacia = true;

foreach ($_GET as $key => $value) {

    if ($key != 'logica' && $value != '') {
	$flag_busqueda_vacia = false;
        break;
    }
} 

//echo $flag_busqueda_vacia;


// SI NO HAY ARGUMENTOS
// HACER FOR EACH COMPROBAR VACIOS
// ponemos un flag

//--------------------------------------------------------------------------
// public_busqueda.php
//
// Genera la página de resultados de busqueda de las publucaciones
// y los exporta al template publicaciones.html
// Los parametros de entrada de busqueda son:
//
//  - tipo : El tipo de publicacion
//  - logica : Coincide algún campo o Todos los campos
//  - campo[1..x] : El tipo de campo a buscar
//  - valor[1..x] : El tipo de valor a buscar
//
// Los valores impresos en página son:
//  - Lista de parametros de busqueda
//  - Lista de referencias encontradas
//--------------------------------------------------------------------------

// definicion de Commons usados
global $public_rel_tipos;
global $public_traduc_campos;
global $public_num_campos;
global $public_por_pagina;
global $public_tipo_links;
global $public_estados_visibles;


// definiciones dependientes del idioma utilizadas
// $public_tipos_refer;
// $public_logica_busqueda
// $gen_separador_campos

// definicion de variables usadas en todo el script
$cadena_busqueda = "";

$numero_pagina = 1;
if(isset($_GET['pagina']) && strlen($_GET['pagina']) > 0)
    $numero_pagina = $_GET['pagina'];


/*
 * MUESTRA EL MENU SI ES EL ADMIN O ALGUN USUARIO
 */
// Si es algún usuario de la intranet
if ($_SESSION['privilegios'] != INVITADO) {

    // Si es el administrador, carga el boton de borrar
    if ($_SESSION['privilegios'] == ADMIN)
        $contenido->parse('content.menu.borrar');

    // Carga el menu
    $contenido->parse('content.menu');
}


//--------------------------------------------------------------------
// LEE LOS SINONIMOS DE LA TABLA
//-------------------------------------------------------------------- 
$lista_sinonimos = array();
$lista_cond_sin  = array();

// Consulta para leer todos los sinonimos
$consulta_sinonimos = 
    "SELECT cadena, valor ".
    "FROM ref_cadenas";

// Realiza consulta para ver campos distintios y verifica si es correcta
if(!($resultado_sinonimos = mysql_query($consulta_sinonimos)))
    ERR_muestra_pagina_error("Error en consulta: $consulta_sinonimos");

// Obtenemos los sinonimos y miramos si alguno esta en la busqueda
while ($sinonimo = mysql_fetch_array($resultado_sinonimos)) {

    $lista_sinonimos[$sinonimo['cadena']] = $sinonimo['valor'];
    // buscamos occurrencias dentro de tabla
    for ($i = 1; $i < $public_num_campos+1; $i++) {

        if (isset($_GET["campo$i"]) && strlen($_GET["campo$i"])>0) {

            if (strpos($sinonimo['valor'],$_GET["valor$i"])) {
                // construye condicion de sinonimos
                $lista_cond_sin[$i] .= "OR valor LIKE '%{$sinonimo['cadena']}%' ";
            }
        }
    }
}
mysql_free_result($resultado_sinonimos);

//--------------------------------------------------------------------
// CONSULTA DE TIPOS DE PUBLICACION
//--------------------------------------------------------------------

$visible = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

// definicion de consultas de la base de datos
$consulta_tipos_referencias =
    "SELECT DISTINCT(tipo) ".
    "FROM referencias ".
    "WHERE visible >= $visible";

// inicializa array de tipos presentes
$tipos_presentes = array();                            

// si la consulta fue bien
if ($resultado_tipos_referencia = mysql_query($consulta_tipos_referencias)) {
    // recorre resultados y registra tipos
    while ($tipo_obtenido = mysql_fetch_array($resultado_tipos_referencia)) {
        $tipo_web = AUX_convertir_tipos($tipo_obtenido['tipo']);
        $tipos_presentes[$tipo_web] = 1;
    }

} else {
    ERR_muestra_pagina_error("Error en consulta: $consulta_tipos_referencias");
}

// bucle de publicacion de cada tipo presente.
for (reset($public_rel_tipos); $tipo_consultado = key($public_rel_tipos);
    next($public_rel_tipos)) {


    if ($tipos_presentes[$tipo_consultado] == 1) {
        // si hay más de uno, inserta elemento en la tabla
        $valores_lista['VAL1'] = $tipo_consultado;
        $valores_lista['VAL2'] = "[{$public_tipos_refer[$tipo_consultado]}]";

        // metelos en el navegador de la página
        $contenido->assign("LISTA1", $valores_lista);
        $contenido->parse("content.directorio.fila");
    }
}
// limpia array de insercion en página
unset($valores_lista);

// termina tabla
$contenido->parse("content.directorio");

//--------------------------------------------------------------------  
// LISTA DE PARAMETROS DE BUSQUEDA
//--------------------------------------------------------------------  

// inicializa variable destino
$lista_parametros = array();
$logica = "";

// inserta lógica en cabecera
if (isset($_GET['logica']) && strlen($_GET['logica']) > 0) {
    $contenido->assign("LOGICA", $public_logica_busqueda[$_GET['logica']]);
    $cadena_busqueda .= "logica={$_GET['logica']}&";

} else {
    $contenido->assign("LOGICA", $public_logica_busqueda['AND']);
    $cadena_busqueda .= "logica=AND&";
}

// inserta en página
$contenido->parse("content.parametros.logica_busqueda");

// para cada uno de los parámetros de busqueda validos.
//for ($i = 1; $i < $public_num_campos+1; $i++) {
for ($i = 1; isset($_GET["campo$i"]) && strlen($_GET["campo$i"]) > 0; $i++) {

    if (isset($_GET["valor$i"]) && strlen($_GET["valor$i"]) > 0) {
        $lista_parametros['CAMPO'] = htmlentities($_GET["campo$i"]);
        $lista_parametros['VALOR'] = $_GET["valor$i"];

        // insertalos en página
        $contenido->assign("LISTA_PARAMETROS",$lista_parametros);
        $contenido->parse("content.parametros.lista_parametros");

        // reconstruye cadena de busqueda
        $cadena_busqueda .= "campo$i={$_GET["campo$i"]}&valor$i={$_GET["valor$i"]}&";
    }
}

// añade campo auxiliar si está definido
if (isset($_GET['campo_aux']) && strlen($_GET['campo_aux']) > 0) {
    // recolecta valores
    $lista_parametros['CAMPO'] = $_GET['campo_aux'];
    $lista_parametros['VALOR'] = $_GET['valor_aux'];

    // insertalo en página
    $contenido->assign("LISTA_PARAMETROS", $lista_parametros);
    $contenido->parse("content.parametros.lista_parametros");

    // reconstruye cadena de busqueda
    $cadena_busqueda .= "campo_aux={$_GET["campo_aux"]}&valor_aux={$_GET["valor_aux"]}&";
}

// añade tipo si está definido
if (isset($_GET['tipo']) && strlen($_GET['tipo']) > 0) {

    // insertalo en página
    $contenido->assign("VALOR", $public_tipos_refer[$_GET['tipo']]);
    $contenido->parse("content.parametros.tipo_publicacion");

    // reconstruye cadena de busqueda
    $cadena_busqueda .= "tipo={$_GET['tipo']}&";
}

// añade identificador si está definido
if (isset($_GET['id_ref_bibtex']) && strlen($_GET['id_ref_bibtex'])) {
    // insertalo en página
    $contenido->assign("VALOR", $_GET['id_ref_bibtex']);
    $contenido->parse("content.parametros.id_bibtex");

    // reconstruye cadena de busqueda
    $cadena_busqueda .= "id_ref_bibtex={$_GET['id_ref_bibtex']}&";
}

// añade estado si está definido
$estados_definidos = "";
$lista_estados = array();
for (reset($public_estados_visibles); $estado_pub = key($public_estados_visibles);
    next($public_estados_visibles)) {

    if ($_GET["E_$estado_pub"] == 1) {
        $estados_definidos .= ' "'.$estado_pub.'"';
        array_push($lista_estados, $estado_pub);
        // reconstruye cadena de busqueda
        $cadena_busqueda .= "E_{$estado_pub}=1&";
    }
}

if (strlen($estados_definidos) > 0) {
    // insertalo en página
    $contenido->assign("VALOR", $estados_definidos);
    $contenido->parse("content.parametros.estado_pub");
}

if (isset($_GET['desde']) && strlen($_GET['desde']) > 0) {
    $cadena_busqueda .= "desde={$_GET['desde']}&";
}

if (isset($_GET['hasta']) && strlen($_GET['hasta']) > 0) {
    $cadena_busqueda .= "hasta={$_GET['hasta']}&";
}

// insertalo en página
$contenido->assign("VALOR", $numero_pagina);
$contenido->parse("content.parametros.pagina");
// finaliza tabla
if ($_SESSION['privilegios'] != INVITADO) 
	$contenido->parse("content.parametros");

//------------------------------------------------------------------
// LISTA PUBLICACIONES ENCONTRADAS
//------------------------------------------------------------------
// Calcula numero mínimo de ocurrencias necesarias para
// mostrar registro
$numero_ocurrencias = AUX_calcula_num_ocurrencias();

// inicializa tipo de cabecera
$tipo_cabecera = "";
$registros_encontrados = 0;

// verifica si se va a realizar una busqueda por cada tipo o solo un tipo
if (isset($_GET['tipo']) && strlen($_GET['tipo']) > 0) {
    $lista_bucle_tipos = array($_GET['tipo']);

} else {
// si no hay definicion de tipo, mete en array todos los tipos
    $lista_bucle_tipos = array_keys($public_rel_tipos);
}

// verifica si existe campo auxiliar
if (isset($_GET["campo_aux"]) && strlen($_GET["campo_aux"]) > 0)
    $campo_auxiliar = $_GET["campo_aux"];

// calcula los registros a mostrar segun el número de página
$pag_lim_inferior = ($numero_pagina-1)*$public_por_pagina;
$pag_lim_superior = $numero_pagina*$public_por_pagina;

// Para cada una de los tipos, haz la consulta de publicaciones
// de esta forma conseguimos la ordenación deseada en los tipos
for (reset($lista_bucle_tipos); 
    ($tipo_consultado = current($lista_bucle_tipos)) && !($flag_busqueda_vacia);
    next($lista_bucle_tipos)) {


    $anyo_publicacion = "";
    
    // construye consulta de obtencion de publicaciones según los parámetros
    // de busqueda
    $consulta_id =
        "SELECT DISTINCT(id_referencia), COUNT(id_campo_ref) AS numero, tipo, ".
        "tipo_link, link_referencia, YEAR(fecha_publicacion) AS anyo, id_campo_ref ".
        "FROM referencias LEFT JOIN ref_relacion ".
        "ON referencias.id_referencia = ref_relacion.id_ref ".
        "LEFT JOIN ref_campos ".
        "ON ref_relacion.id_campos = ref_campos.id_campo_ref ";

    $condicion_busqueda =
        "WHERE visible >= $visible AND ".AUX_condicion_tipos($tipo_consultado).
        AUX_condicion_bibtex($_GET['id_ref_bibtex']).AUX_condicion_estado($lista_estados).
        AUX_condicion_campos($lista_cond_sin);

    if(isset($_GET['desde']) && strlen($_GET['desde']) > 0)
        $condicion_busqueda .= "AND YEAR(fecha_publicacion) >= '{$_GET['desde']}' ";

    if(isset($_GET['hasta']) && strlen($_GET['hasta']) > 0)
        $condicion_busqueda .= "AND YEAR(fecha_publicacion) <= '{$_GET['hasta']}' ";


    $agrupacion = "GROUP BY id_referencia ";
    $ordenacion = "ORDER BY fecha_publicacion DESC";


    // Construye consulta completa
    $consulta_id .= $condicion_busqueda.$agrupacion.$ordenacion;
    // realiza consulta de seleccion de id's
    
    if (!($resultado_id = mysql_query($consulta_id))) {
        ERR_muestra_pagina_error("Error en consulta: $consulta_id");

    } else {
        // para cada id obtenido
        while ($id_referencia = mysql_fetch_array($resultado_id)) {
            // no imprimas valor si el numero de ocurrencias
            // es menor del esperado y la lógica es AND
            if (($id_referencia['numero'] < $numero_ocurrencias)
                && ($_GET['logica']=='AND')) {
                continue;
            }
            // incrementa el número de registros insertados
            $registros_encontrados ++;

            // muestra solo aquellos que esten en la página
            if (($pag_lim_inferior < $registros_encontrados)
                && ($registros_encontrados <= $pag_lim_superior)) {
                // inicializa array de publicaciones
                $campos_public = array();
                $tipo_web_actual = AUX_convertir_tipos($id_referencia['tipo']);

                // verifica si tiene que insertar cabecera de tipo
                if ($tipo_cabecera != $tipo_web_actual) {

                    // termina tabla anterior si no es la primera
                    if (strlen($tipo_cabecera) > 0) {
                        $contenido->parse("content.publicaciones.lista");
                        $contenido->parse("content.publicaciones");
                    }

                    // asigna tipo
                    $tipo_cabecera = $tipo_web_actual;
                    // mete tipo en la página
                    $contenido->assign("TIPO_PUBLICACION",
                        $public_tipos_refer[$tipo_cabecera]);
                    $contenido->parse("content.publicaciones.cabecera_tipo");
                }
                
                // construye segunda consulta para obtener campos de la publicacion
                $consulta_campos = 
                    "SELECT campo, valor ".
                    "FROM ref_campos ".
                    "WHERE id_campo_ref={$id_referencia['id_campo_ref']}";

                // obtiene campos de la publicacion seleccionada
                

                if (! ($resul_campos = mysql_query($consulta_campos, $conexion))) {
                    ERR_muestra_pagina_error("Error en consulta: $consulta_campos");
                
                } else {
                    
                    // obtiene campos y los inserta en el array asociativo
                    while ($campos_fila = mysql_fetch_array($resul_campos)) {
                        $campos_public[$campos_fila['campo']] = $campos_fila['valor'];
                        AUX_sustitucion_sinonimos ($lista_sinonimos,
                            $campos_public[$campos_fila['campo']]);
                    }

                    // si tiene un campo crossref, obtiene sus campos
                    if (strlen($campos_public['crossref']) > 0) {
                        // construye consulta
                        $consulta_crossref = 
                            "SELECT campo, valor ".
                            "FROM ref_relacion ".
                            "LEFT JOIN ref_campos ".
                            "ON ref_relacion.id_campos = ref_campos.id_campo_ref ".
                            "WHERE referencia_cruzada = 1 AND id_ref = {$id_referencia['id_referencia']}";

                        // ejecutala y obtiene campos nuevos
                        if ($resul_campos = mysql_query($consulta_crossref, $conexion)) {

                            while($campos_fila = mysql_fetch_array($resul_campos)) {
                                // asigna solo si no hay ya un campo del hijo
                                if (strlen($campos_public[$campos_fila['campo']]) == 0) {
                                    $campos_public[$campos_fila['campo']] = $campos_fila['valor'];
                                    AUX_sustitucion_sinonimos ($lista_sinonimos,
                                        $campos_public[$campos_fila['valor']]);
                                }
                            }

                        } else {
                            ERR_muestra_pagina_error("Error en consulta: $consulta_crossref");
                        }
                    }

                    // si el año a cambiado respecto al anterior,
                    // inserta nueva cabecera de año
                    if ($id_referencia['anyo'] != $anyo_publicacion) {
                        // cierra la lista anterior
                        $contenido->parse("content.publicaciones.lista");

                        // asigna nuevo año
                        $anyo_publicacion = $id_referencia['anyo'];
                        $anyo_public_str = str_replace("9999", "---", $anyo_publicacion);

                        // inserta cabecera de año y cierra lista
                        $contenido->assign("ANYO_PUBLICACION",$anyo_public_str);
                        $contenido->parse("content.publicaciones.lista.cabecera_anyo");
                    }

                    // inserta la cadena formateada en la pagina
                    $contenido->assign("REFERENCIA",
                        AUX_campos_formateados($campos_public, "", $gen_separador_campos));

                    
                    // Si es usuario, muestra el boton de editar
                    if ($_SESSION['privilegios'] != INVITADO) {
                        $contenido->assign("ID_REFERENCIA", $id_referencia['id_referencia']);
                        $contenido->parse('content.publicaciones.lista.fila.editar');
                    }


                    if ($id_referencia['tipo_link'] == $public_tipo_links['Interno']) {
                        $nombre_fichero = substr($id_referencia['links_referencia'],
                            strrpos($id_referencia['links_referencia'],"/"));
                        $contenido->assign("LINK_PUB", "$public_dir_docs{$id_referencia['link_referencia']}");
                    
                    } else {
                        $contenido->assign("LINK_PUB", $id_referencia['tipo_link']);
                    }

                    // asigna Link a publicacion si lo tiene
                    if ($id_referencia['tipo_link'] != $public_tipo_links['No Disponible']) {
                        $extension = strtoupper(
                            substr($id_referencia['link_referencia'], strrpos($id_referencia['link_referencia'], ".")+1));
                        $contenido->assign("EXT", "[{$extension}]");

                    } else {
                        $contenido->assign("EXT", "");
                    }

                    // escribe resultados en página
                    $contenido->parse("content.publicaciones.lista.fila");

                    // libera resultados de la consulta
                    mysql_free_result ($resul_campos);
                }
            } // If de chequeo de consulta
        } // Cierra el bucle de consulta de cada registro
    } // If de chequeo de consulta
} // Cierra el bucle FOR de cada tipo consultado

// cierra la lista de publicaciones
$contenido->parse("content.publicaciones.lista");
$contenido->parse("content.publicaciones");

/* RESUMEN DE PAGINAS */
// inserta el numero de registros insertados
if ($registros_encontrados == 0) {
    $contenido->parse("content.resumen.resumen_negativo");

} else {
    // Obtiene el primer y el último registro mostrados
    $primera_mostrada = $pag_lim_inferior+1;
    $ultima_mostrada = 
      ($registros_encontrados > $pag_lim_superior) ? $pag_lim_superior : $registros_encontrados ;

    $lista_resumen = array ( 'TOTAL'   => $registros_encontrados,
                             'PRIMERA' => $primera_mostrada,
                             'ULTIMA'  => $ultima_mostrada);
    $contenido->assign('LISTA', $lista_resumen);
    $contenido->parse('content.resumen.resumen_positivo');

    // Calcula la última página
    $ultima_pagina = ceil($registros_encontrados / $public_por_pagina);

    // Asigna los enlaces de las primeras páginas
    if ($numero_pagina != 1) {
      $contenido->assign('ENLACE_PRIMERA', $cadena_busqueda);
      $contenido->assign('ENLACE_ANTERIOR', $cadena_busqueda."pagina=".($numero_pagina-1));
      $contenido->parse('content.resumen.primeras');
    }

    if ($numero_pagina != $ultima_pagina) {
      $contenido->assign('ENLACE_SIGUIENTE', $cadena_busqueda."pagina=".($numero_pagina+1));
      $contenido->assign('ENLACE_ULTIMA', $cadena_busqueda."pagina=".$ultima_pagina);
      $contenido->parse('content.resumen.ultimas');
    }

    // Obtenemos entre las páginas que nos vamos a mover
    $numero_paginas = $ultima_pagina;
    $pag_mostrar_primera = 1;
    $pag_mostrar_ultima = $numero_paginas;
    // Si el numero de paginas a mostrar es mayor que las paginas que queremos mostrar
    if($numero_paginas > $public_num_paginas) {
        // Vemos si esta al inicio, para saber donde acabamos
        if ($numero_pagina <= ceil($public_num_paginas/2)) {
            $pag_mostrar_ultima = $public_num_paginas;

            // Si esta al inicio, y como hay mas, ponemos los puntos suspensivos en la siguiente
            $contenido->assign("ENLACE_PAGINA", $cadena_busqueda."pagina=".($pag_mostrar_ultima+1));
            $contenido->parse("content.resumen.siguientes");

            // Si esta al final, vemos cual va a ser la primera en mostrarse
        } elseif ($numero_pagina >= ($numero_paginas - floor($public_num_paginas/2)) ) {
            $pag_mostrar_primera = $numero_paginas - $public_num_paginas + 1;
            $contenido->assign("ENLACE_PAGINA", $cadena_busqueda."pagina=".($pag_mostrar_primera-1));
            $contenido->parse("content.resumen.anteriores");

            // Si esta en el centro, vemos cual va a ser la primera y la ultima controlando
            // el caso en que el numero de paginas a mostrar es par
        } else {
            $pag_mostrar_primera = $numero_pagina - floor($public_num_paginas/2);
            // Si es par, eliminamos una por la izquierda para cuadrar, es decir, sumamos
            if($public_num_paginas % 2 == 0)    $pag_mostrar_primera++;

            $pag_mostrar_ultima = $numero_pagina + floor($public_num_paginas/2);

            $contenido->assign("ENLACE_PAGINA",$cadena_busqueda."pagina=".($pag_mostrar_primera-1));
            $contenido->parse("content.resumen.anteriores");
            $contenido->assign("ENLACE_PAGINA",$cadena_busqueda."pagina=".($pag_mostrar_ultima+1));
            $contenido->parse("content.resumen.siguientes");
        }
    }

    // Imprimimos las páginas
    for($pag = $pag_mostrar_primera; $pag <= $pag_mostrar_ultima; $pag++) {
        // Creamos el enlace con su respectivo numero
        $contenido->assign("ENLACE_PAGINA",$cadena_busqueda."pagina=".$pag);
        $contenido->assign("PAGINA", $pag);

        // y vemos donde lo cargamos y como, si antes, después o si es la actual
        if($pag < $numero_pagina) {
            $contenido->parse("content.resumen.anterior");
        } elseif ($pag == $numero_pagina) {
            $contenido->parse("content.resumen.actual");
        } else {
            $contenido->parse("content.resumen.siguiente");
        }
    }

}

$contenido->assign('ENLACE_EXPORT', $cadena_busqueda);

// imprime resumen
$contenido->parse('content.resumen');

// Cierra la conexion con mysql
mysql_close($conexion);


/*
 * MUESTRA LA PAGINA
 */
// Parsea el contenido
$contenido->parse("content");

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>
