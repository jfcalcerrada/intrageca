<?php
// Inicializamos el archivo con el script
include('common/init.php');

include('common/common_pub.php');
include('public_insertar.php');


//--------------------------------------------------------------------------
// public_editar.php
//
// Genera la página de edición de una publicacion específica
// que se le pasa por parámetro.
// 
// Los parámetros que necesita la página son:
//
//   id_ref: Identificador del registro a editar
//--------------------------------------------------------------------------

// array donde se almacenan los registros
$registros = array();


//--------------------------------------------------------------------
// VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
//--------------------------------------------------------------------
// le hemos dado a actualizar al formulario, modificando valores
if ($_POST['modificado'] == 1) 
{
    // llama a la funcion de insertar/actualizar
    $id_referencia = public_insertar($conexion, $_POST);
}
// le hemos dado a actualizar al formulario, sin modificar valores
else if (strlen($_POST['id_ref']) > 0) // 
{
    $id_referencia = $_POST['id_ref'];
} 
// para el caso de un enlace a la página de editar
else
{
    $id_referencia = $_GET['id_ref'];
}

//--------------------------------------------------------------------
// OBTIENE LOS VALORES DE PUBLICACION DE LA BASE DE DATOS
//--------------------------------------------------------------------
if ($id_referencia >0)
{   
    // Crea La Consulta De La Referencia Si Está Definida
    $consulta_public = "SELECT id_ref_bibtex, tipo_bibtex, visible, tipo_link,".
     "link_referencia, fecha_publicacion, idioma, estado FROM referencias ".
     "WHERE id_referencia=".$id_referencia;

    // ejecuta la consulta para obtener datos
    $resultado = mysql_query($consulta_public, $conexion);

    if ($resultado)
    {$registros = mysql_fetch_row($resultado);}
    else
    {echo "Error al realizar la consulta ".$consulta_public;}
}

//--------------------------------------------------------------------
// RELLENA FORMULARIO DE BORRADO SI NO ES NUEVO
//-------------------------------------------------------------------- 
if ($id_referencia != 0)
{
    $contenido->assign("ID_REFERENCIA", $id_referencia);
    $contenido->parse("content.form_borrar");
}
//--------------------------------------------------------------------
// RELLENA LOS VALORES DE PUBLICACION
//-------------------------------------------------------------------- 
// Rellena los distintos tipos web
for (reset($public_rel_tipos);
    $tipo_web = key($public_rel_tipos);
    next($public_rel_tipos))
{
    $lista_tipos = $public_rel_tipos[$tipo_web];
    // por cada tipo BIBTEX dentro del tipo WEB
    for (reset($lista_tipos);
        $tipo_bibtex = current($lista_tipos);
        next($lista_tipos))
    {
        $tipo_pub_bibtex = $registros[1];
        // chequea si es el tipo de la publicacion
        $selected = ($tipo_bibtex == $registros[1])? "SELECTED":"";

        $contenido->assign("TIPO_SELEC", $tipo_bibtex);
        $contenido->assign("TIPO_LARGO_SELEC",$tipo_web." (".$tipo_bibtex.")");
        $contenido->assign("SELECTED",$selected);
        $contenido->parse("content.form_publicacion.datos_pub.select_tipo");
    }
}

// Rellena valores de estados de publicacion
for (reset($public_estados_visibles);
    $estado = key($public_estados_visibles);
    next($public_estados_visibles))
{
    // chequea si es el estado seleccionado
    $selected = ($estado == $registros[7])? "SELECTED":"";

    $contenido->assign("ESTADO_SELEC",$estado);
    $contenido->assign("SELECTED",$selected);
    $contenido->parse("content.form_publicacion.datos_pub.select_estado");
}

// imprime los idiomas disponibles
foreach($gen_idiomas_disp as $cod => $texto_idioma)
{
    // selecciona elementos
    $selected = ($registros[6] == strtoupper($cod))? "SELECTED":"";
    // asigna lista
    $lista_idioma = array ( 'IDIOMA_SELEC' => strtoupper($cod),
                             'IDIOMA_WEB'   => $texto_idioma,
                             'SELECTED'     => $selected);
    // insertalo en página
    $contenido->assign('LISTA',$lista_idioma);
    $contenido->parse('content.form_publicacion.datos_pub.select_idioma');
}    

// Rellena los valores de tipo de links
for (reset($public_tipo_links);
    $tipo_link = key($public_tipo_links);
    next($public_tipo_links))
{ 
    // chequea si es el estado seleccionado
    $selected = ($public_tipo_links[$tipo_link] == $registros[3])? "SELECTED":"";

    $contenido->assign("V_TIPO_LINK",$public_tipo_links[$tipo_link]);
    $contenido->assign("TIPO_LINK",$tipo_link);
    $contenido->assign("SELECTED",$selected);
    $contenido->parse("content.form_publicacion.datos_pub.select_tipo_link");
}

// asigna link interno, externo o ninguno
if ($registros[3] == $public_tipo_links['Externo'])
{
    $contenido->assign("LINK_REFER",$registros[4]);
    $contenido->parse("content.form_publicacion.datos_pub.link_externo");
}
else if ($registros[3] == $public_tipo_links['Interno'])
{
    $nombre_fichero = substr($id_referencia[4],
        strrpos($id_referencia[4],"/"));
    $contenido->assign("LINK_REFER",'docs/'.$registros[4]);
    $contenido->parse("content.form_publicacion.datos_pub.link_interno");
}

// verifica la visibilidad
$visible = ($registros[2] == 1)? "CHECKED":"";
// pon Warning si es un valor de fecha y visibilidad invalido
$aviso = (($registros[2] == 1)&&($registros[5] == '9999-01-01'))?
     "Referencia publicada sin fecha de publicaci&oacute;n":"";

// asigna los demas valores a lista general 
$valores_lista = array ( 
         "ID_REFERENCIA" => $id_referencia,
         "ID_REF_BIBTEX" => $registros[0],
         "VISIBLE" => $visible,
         "AVISO" => $aviso);

// inserta los valores en página
$contenido->assign("LISTA",$valores_lista);
$contenido->parse("content.form_publicacion.datos_pub");

//--------------------------------------------------------------------
// RELLENA LOS VALORES DE CAMPOS
//-------------------------------------------------------------------- 
// contador de número de campos
$num_campos = 0;

if ((strlen($id_referencia) > 0) AND ($id_referencia > 0))
{
    // crea consulta de seleccion de campos
    $consulta_campos = 'SELECT campo, valor '.
      'FROM ref_relacion LEFT JOIN ref_campos '.
      'ON ref_relacion.id_campos=ref_campos.id_campo_ref '.
      'WHERE referencia_cruzada=0 AND id_ref ='.$id_referencia.
      ' ORDER BY id_campo';

    // ejecuta la consulta
    $resultado = mysql_query($consulta_campos, $conexion);

    if (!$resultado)
    {
        echo "Error al realizar la consulta ".$consulta_public;
    }
    else
    {

//        // Array que contiene TODOS los campos Bibtex y ademas en orden
//        $public_orden_campos;
//
//        // Las introducimos como claves que refencian strings vacios
//        foreach ($public_orden_campos as $key) {
//            $campos_bibtex[$key] = '';
//        }


        //$campos_bibtex = array_fill_keys($public_orden_campos, '');

        //print_r($campos_bibtex);
        
        // Obtenemos cada uno de los registros para rellenar el array
        while ($registros = mysql_fetch_array($resultado))
            $campos_bibtex[$registros['campo']] = $registros['valor'];

        // Los imprimimos todos
        foreach($campos_bibtex as $campo => $valor) {

            // Comprobamos si el campo es OPT o no
            $modifica = ((strpos($campo, 'OPT') == 0) AND
                !(strpos($campo, 'OPT') === false))? "Quitar": "Poner";


            // Por defecto los pone todos como OPT -> quitar dado que en este caso
            // solo tenemos que evaluar una cosa
            $modifica = "Quitar";
            $warning = "";

            // Si no es OPT cambiamos el $modifica y miramos si existe el campo
            if(!((strpos($campo, 'OPT') == 0) && !(strpos($campo, 'OPT') === false))) {
                $modifica = "Poner";
                
                if (!in_array($campo, $public_orden_campos))
                    echo $warning = "El campo definido no existe";
            }

            // Verifica que es un campo que no puede permutar
            if (($campo == 'idioma') || ($campo == 'estado')) {
                $disabled = "DISABLED";

            } else {
                $disabled = "";
            }

            // Aumentamos el numero de campo para poder procesar el formulario
            $num_campos ++;
            
            // Asigna valores a la lista
            $lista_valores = array (
              'INDICE' => $num_campos,
              'MODIFICAR' => $modifica,
              'CAMPO' => $campo,
              'VALOR' => $valor,
              'DISABLED' => $disabled,
              'WARNING' => $warning);

            // Inserta campo en la página
            $contenido->assign("LISTA",$lista_valores);
            $contenido->parse("content.form_publicacion.campos_pub.fila");
        }

    }
}

// Cierra tabla para con el número de registros
$contenido->assign("NUM_CAMPOS", $num_campos);
$contenido->parse("content.form_publicacion.campos_pub");

//imprime resultado
$contenido->parse("content.form_publicacion");

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
