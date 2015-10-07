<?php
// Inicializamos el archivo con el script
include('common/init.php');

include('common/common_pub.php');

//--------------------------------------------------------------------------
// publicaciones.php
//
// Genera la página principal de publicaciones presentes
// segun se define en common_pub.php y los exporta al template
// publicaciones.html
// 
// Los tipos exportados a la página son:
// -  La lista de tipos de publicacion que tienen al menos una publicacion
//    en la base de datos
// -  La lista de distintos campos por los que se puede realizar una
//    busqueda
//--------------------------------------------------------------------------


// definicion de Commons usados
global $public_rel_tipos;
global $public_traduc_campos;
global $public_num_campos;
global $public_estados_visibles;


// definiciones dependientes del idioma utilizadas
// $public_tipos_refer;

// crea array para almacenar valores en lista
$valores_lista = array();


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
// CONSULTA DE TIPOS DE PUBLICACION
//-------------------------------------------------------------------- 

$visible = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

// contador de nº de publicaciones
$numero_publicaciones = 0;

// definicion de consultas de la base de datos
$consulta_gen =
    "SELECT COUNT(id_referencia) AS numero ".
    "FROM referencias ".
    "WHERE visible >= $visible AND ";

// bucle de consulta de cada elemento de la lista.
for (reset($public_rel_tipos); $tipo_consultado = key($public_rel_tipos);
    next($public_rel_tipos)) {

    // construye consulta para el tipo
    $consulta_tipo = $consulta_gen.AUX_condicion_tipos($tipo_consultado);
    // realiza consulta
    $resultado_tipo = mysql_query($consulta_tipo);

    // Miramos si la consulta se produce correctamente
    if ($resultado_tipo = mysql_query($consulta_tipo)) {

        $numero_registros = mysql_fetch_array($resultado_tipo);

        // Si el tipo de publicacion tiene alguna entrada
        if ($numero_registros['numero'] > 0) {

            // añade valor al numero de publicaciones
            $numero_publicaciones += $numero_registros['numero'];

            // si hay más de uno, inserta elemento en la tabla
            $valores_lista['VAL1'] = $tipo_consultado;
            $valores_lista['VAL2'] = $public_tipos_refer[$tipo_consultado].
                                                 " ({$numero_registros['numero']})";
            // metelos en el directorio de la página
            $contenido->assign('LISTA1', $valores_lista);
            $contenido->parse('content.publicaciones.tipo');

            // metelos en el SELECT del buscador de la página
            $valores_lista['VAL2'] = $public_tipos_refer[$tipo_consultado];
            $contenido->assign('LISTA2',$valores_lista);
            $contenido->parse('content.select_tipos');
        }

    } else {
        ERR_muestra_pagina_error("Error en consulta: $consulta_tipo");
    }
}

// inserta el total
$contenido->assign('NUM_PUBLICACIONES', $numero_publicaciones);
// termina tabla
$contenido->parse('content.publicaciones');

// limpia array de insercion en página
unset($valores_lista);


/*
 * BUSQUEDA POR IDENTIFICADOR BIBTEXT, SOLO USUARIOS
 */
if ($_SESSION['privilegios'] != INVITADO)
    $contenido->parse('content.id_bibtex');


//--------------------------------------------------------------------
// SELECCION DE ESTADOS DE PUBLICACION
//--------------------------------------------------------------------
if ($_SESSION['privilegios'] != INVITADO) {
    for (reset($public_estados_visibles); $estado_pub = key($public_estados_visibles);
        next($public_estados_visibles)) {

        $valores_lista['VAL1'] = $estado_pub;
        $valores_lista['VAL2'] = strtolower($estado_pub);
        $contenido->assign("LISTA2", $valores_lista);
        $contenido->parse("content.estado_public.select_estados");
    }
    
    $contenido->parse('content.estado_public');
}

//--------------------------------------------------------------------
// CONSULTA DE CAMPOS DE BUSQUEDA
//-------------------------------------------------------------------- 
// inicializacion de variables
$valores_lista = array();
$valores_prv_lista = array();
$num_campos = 0;
$num_campos_pv = 0;

// definicion de consultas de la base de datos
$consulta_campos =
    "SELECT DISTINCT campo ".
    "FROM ref_campos ".
    "ORDER BY campo ASC";

// realiza consulta para ver campos distintos
if (!($resultado_campos = mysql_query($consulta_campos)))
    ERR_muestra_pagina_error("Error en consulta: $consulta_campos");


// obtiene resultados de los distintos campos de la BD
while ($campo = mysql_fetch_array($resultado_campos)) {

    // si el campo está en la lista de traducidos, inserta su traducción
    if (array_key_exists($campo['campo'], $public_traduc_campos)) {
        // inserta campo en array
        $valores_lista[$num_campos]['VAL1'] = $campo['campo'];

        // incrementa el número de campos
        $num_campos++;

    } else {
        $valores_prv_lista[$num_campos_pv]['VAL1'] = $campo['campo'];

        // incrementa el número de campos
        $num_campos_pv++;
    }
}

// asigna campos a la pagina
for ($i = 1; $i < $public_num_campos+1; $i++) {

    if (count($valores_lista) > 0) {

        // procesa select dentro de lista
        foreach ($valores_lista as $lista) {
            $contenido->assign("LISTA3",$lista);
            $contenido->parse("content.select_campo.lista");
        }

        // inserta el nombre de los campos
        $id_campos = array('CAMPO'    => "Campo",
                           'NOMBRE_C' => "campo$i",
                           'NOMBRE_V' => "valor$i");
        $contenido->assign("LISTA3", $id_campos);
        $contenido->parse("content.select_campo");
    }
}

// asigna campo auxiliar intranet (no publicos y todos los OPT)
if (count($valores_prv_lista) > 0) {

    foreach ($valores_prv_lista as $lista) {
        $contenido->assign("LISTA3",$lista);
        $contenido->parse("content.select_campo.lista");
    }

    // inserta el nombre de los campos
    $id_campos = array('CAMPO'    => 'Campo Auxiliar',
                       'NOMBRE_C' => "campo_aux",
                       'NOMBRE_V' => "valor_aux");
    $contenido->assign("LISTA3", $id_campos);
    $contenido->parse("content.select_campo");
}                     


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