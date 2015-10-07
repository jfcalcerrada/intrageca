<?
// Definicion de numero de campos de busqueda de bibliografia
$public_num_campos = 3;

// Definicion del número de publicaciones por página
$public_por_pagina = 15;

// Número de páginas a mostrar en búsqueda de publicaciones
$public_num_paginas = 5;

// Definición de estados de las publicaciones y su visibilidad
$public_estados_visibles = array (
      'DRAFT' => 0 , 'ENVIADO' => 0, 'RECHAZADO' => 0, 'ACEPTADO' => 1,
      'PUBLICADO' => 1);

$public_estado_defecto = 'PUBLICADO';

// definicion de tipos de links
$public_tipo_links = array (
  'No Disponible' => 'N', 'Externo' => 'E', 'Interno' => 'I');

// Definicion de todos los tipos bibtex
$public_tipos_bibtex = array ('BOOK','INBOOK','ARTICLE','PROCEEDINGS',
 'INPROCEEDINGS','CONFERENCE','COLLECTION','INCOLLECTION','PATENT',
 'PHDTHESIS','MASTERSTHESIS','MISC','UNPUBLISHED','BOOKLET','MANUAL',
 'TECHREPORT');

//-- Definicion de tipos de publicaciones
// La agrupación de tipos será la siguiente:
//  - 	Revistas (Article)
//  - 	Colecciones (Collection, InCollection)
//  - 	Congresos (Proceedings, InProceedings, Conference)
//  - 	Libros (Book) y Capítulos de Libros (InBook)
//  - 	Patentes (Patent)
//  - 	Proyecto Fin de Carrera (MasterThesis)
//  - 	Tesis Doctoral (PHDThesis)
//  - 	Otros (Misc, Unpublished, Booklet, Manual, TechReport) 
$public_rel_tipos = array (
    'LIBROS'        => array('BOOK', 'INBOOK'),
    'REVISTAS'      => array('ARTICLE'),
    'CONGRESOS'     => array('PROCEEDINGS', 'INPROCEEDINGS', 'CONFERENCE'),
    'COLECCIONES'   => array('COLLECTION', 'INCOLLECTION'),
    'PATENTES'      => array('PATENT'),
    'TESIS'         => array('PHDTHESIS'),
    'PFC'           => array('MASTERSTHESIS'),
    'OTROS'         => array('MISC', 'UNPUBLISHED', 'BOOKLET', 'MANUAL',
                             'TECHREPORT')
); 

// -- Definicion de orden de aparición de los campos en una referencia
//  author, title, year, month, edition, pages, editor,
//  booktitle, ISBN, chapter, journal, number, volume,
//  publisher, institution, organization
$public_orden_campos = array(
    'author', 'title', 'chapter', 'edition', 'editor', 'booktitle', 'series',
    'journal', 'volume', 'number', 'pages', 'year', 'month', 'publisher',
    'institution', 'organization', 'address', 'note', 'doi', 'ISBN', 'ISSN',
    'annote');


$public_estilo = array(
    'html'  => array('B' => 'bold', 'I' => 'italic', 'U' => 'underline'),
    'rtf'   => array('B' => '\b', 'I' => '\i', 'U' => '\ul')
);


// -- Definicion de campos requeridos y opcionales para cada tipo bibtex
/*    Nota: book => author or editor,
 *          inbook => author or editor, pages or chapter
 *    Nota: diferencia entre inproceeding y conference
 */
$public_campos_bibtex = array (
    'DEFAULT' => array('author'=>'*', 'title'=>'*', 'year'=>'c'),
    'BOOK' => array('author'=>'editor', 'editor'=>'author', 'publisher'=>'*'/*,
                    'year'=>'o', 'month'=>'o', 'edition'=>'o',
                    'adress'=>'o', 'ISBN'=>'o'*/),
    'INBOOK' => array('author'=>'editor', 'editor'=>'author', 'publisher'=>'*'/*,
                    'year'=>'o', 'month'=>'o', 'edition'=>'o',
                    'adress'=>'o', 'ISBN'=>'o',
                    'pages'=>'chapter', 'chapter'=>'pages'*/),
    'ARTICLE' => array('journal'=>'*'/*,
                    'year'=>'o', 'month'=>'o', 'volume'=>'o',
                    'number'=>'o', 'pages'=>'o'*/),
    'PROCEEDINGS' => array('editor'=>'organization', 'organization'=>'editor',
                    'author'=>'n'/*, 'year'=>'o', 'month'=>'o'
                    'location'=>'o', 'adress'=>'o', */),
    'INPROCEEDINGS' => array('booklet'=>'*'),
    'CONFERENCE' => array('booklet'=>'*'),
    'COLLECTION' => array(),
    'INCOLLECTION' => array('booklet'=>'*', 'publisher'=>'*'),
    'PATENT' => '',
    'PHDTHESIS' => array('school'=>'*'),
    'MASTERSTHESIS' => array('school'=>'*'),
    'MISC' => '',
    'UNPUBLISHED' => array('note'=>'*'),
    'BOOKLET' => array('author'=>'n'),
    'MANUAL' => array('author'=>'n'),
    'TECHREPORT' => array('institution'=>'*')
);

// -- Definicion de prefijos y sufijos para cada campo de una referencia.
//    El prefijo se antepondrá al campo y el sufijo se incorporará
//    al final del campo. 
//    Ej. Para mostrar un campo en negrita: prefijo=<B> , sufijo=</B>
// 
$public_marcacion_campos = array (
    'author' => array( 'prefijo'=>'<strong>', 'sufijo'=> '</strong>'),
    'title'  => array( 'prefijo'=>'<em>', 'sufijo'=> '</em>'),
    'doi'    => array( 'prefijo'=>' doi: ', 'sufijo'=> ''),
    'ISBN'   => array( 'prefijo'=>' ISBN: ', 'sufijo'=> ''),
    'ISSN'   => array( 'prefijo'=>' ISSN: ', 'sufijo'=> ''),
    'pages'  => array( 'prefijo'=>'', 'sufijo'=> ' pag'),
    'number' => array( 'prefijo'=>' Num. ', 'sufijo'=> ''),
    'volume' => array( 'prefijo'=>' Vol. ', 'sufijo'=> ''));

    
function cargar_formatos($public_marcacion_campos, $id_miembro) {
    GLOBAL $public_formatos;

    $consulta_formato =
        "SELECT tipo, author, title, other ".
        "FROM formato_bibtex ".
        "WHERE id_miembro IN (0)".//, $id_miembro) ".
        "ORDER BY id_miembro DESC";

    $resultado = mysql_query($consulta_formato);

    if(!($fila = mysql_fetch_array($resultado))) {
        echo "Error en la búsqueda";
        exit;
    }

    $public_marcacion_campos['author']['prefijo'] = $public_formatos[$fila['author']]['html'];
    $public_marcacion_campos['author']['sufijo'] = str_replace("<", "</", $public_formatos[$fila['author']]['html']);

    $public_marcacion_campos['title']['prefijo'] =$public_formatos[$fila['title']]['html'];
    $public_marcacion_campos['title']['sufijo'] = str_replace("<", "</", $public_formatos[$fila['title']]['html']);

    return $public_marcacion_campos;

}

//------------------------------------------------------------------
// DEFINICION DE MANEJADORES DE LOS DATOS (NO TOCAR)
//------------------------------------------------------------------

//------------------------------------------------------------------
// Funcion: AUX_estado_es_visible
//
//  Esta funcion chequea si el estado que se le pasa por parámetro
//  es valido y si es visible. 
//
//  Devuelve:
//      1 - si el estado es válido y visible
//      0 - si el estado es válido pero no visible
//     -1 - si el estado es invalido
function AUX_estado_es_visible ($estado)
{
  // inicializacion de Common
  global $public_estados_visibles;
   
  // verificar si el estado es valido
  if (array_key_exists($estado, $public_estados_visibles))
  {
    return $public_estados_visibles[$estado];
  }
  else
  {
    // devuelve que no existe
    return -1;
  }
   
}

//------------------------------------------------------------------
// Funcion: AUX_convertir_tipos
//
//  Esta funcion convierte los tipos de publicacion internos a
//  los tipos externos manejados por la web
//
function AUX_convertir_tipos($tipo_bibtex)
{
  // inicializacion de Common
  global $public_rel_tipos;
  
  foreach ($public_rel_tipos as $tipo_web => $array_web)
  {
      if (in_array($tipo_bibtex,$array_web))
      {
      	return  $tipo_web;
      }
  } 
}

//------------------------------------------------------------------
// Funcion: AUX_calcula_num_ocurrencias
//
//  Esta función calcula el numero de ocurrencias que debe tener
//  un registro para que se muestre en la página.
//  La lógica de la función será:
//    OR : 1 ocurrencia
//   AND : tantas ocurrencias como número de campos distintos haya.
//
function AUX_calcula_num_ocurrencias()
{
   // importa el numero de campos
   global $public_num_campos;
   
   // si logica es OR, devuelve 1
   if ($_GET['logica'] == 'OR')
   {
      return 1;
   }
   else
   {
      $num_campos = 0;
      // chequea cuantos campos se han incluido sobre el máximo
      for ($i=1; $i< $public_num_campos+1; $i++)
      {
         if (strlen($_GET["campo$i"])>0) $num_campos++;
      }
           
      // compara cuantos campos son iguales
      // resta 1 por cada campo distinto
      for ($i=1; $i< $public_num_campos; $i++)
      {
       for ($j=$i+1; $j< $public_num_campos+1; $j++)
       {
         if ($_GET["campo$i"] == $_GET["campo$j"]) $num_campos--;
       } 
      }
      // devuelve valor
      return $num_campos;
   }
}
//------------------------------------------------------------------
// Funcion: AUX_condicion_bibtex
//
//  Esta funcion construye la consulta SQL de condicion para 
//  el identificador de bibtex.
function AUX_condicion_bibtex($id_bibtex)
{
   $condicion_bibtex = "";
   // solo si el identificador BIBTEX es distinto de 0
   if (strlen($id_bibtex)>0)
   {
      $condicion_bibtex= " AND (id_ref_bibtex LIKE '%".$id_bibtex."%')";
   }
   return $condicion_bibtex;
}

//------------------------------------------------------------------
// Funcion: AUX_condicion_estado
//
//  Esta funcion construye la consulta SQL de condicion para 
//  un estado definido.
function AUX_condicion_estado($lista_estados)
{
   $condicion_estado = "";
   if (count($lista_estados)> 0)
   {
      $condicion_estado = " AND (";
      $logica = " ";
      for (reset($lista_estados); 
           $estado = current($lista_estados);
           next($lista_estados))
      
      {
         $condicion_estado .= $logica.'estado="'.$estado.'"';
         $logica = " OR ";
      }
      $condicion_estado .= ") ";
   }
   return $condicion_estado;
}


//------------------------------------------------------------------
// Funcion: AUX_condicion_campos
//
//  Esta funcion construye la consulta SQL de condicion para 
//  los campos de busqueda.
//  Construye condiciones de busqueda segun campos como una concatenacion
//  de ((CAMPO1 AND (VALOR1 $logica VALOR2)) OR (CAMPO3 AND VALOR3)
//  En caso que el valor sea ANY, solo pone la condicion de valor
//  Tambien soporta un campo auxiliar 'campo_aux' de busqueda.
//  Nota: Campos iguales se concatenan con AND
//
//  Parametros de entrada
//   lista_sinonimos: Array con una lista de sinonimos a incorporar en
//   la busqueda.
function AUX_condicion_campos($lista_sinonimos)
{
   // importa el numero de campos
   global $public_num_campos;
   $campos = array();
   $condicion_campos = "";
   
   // selecciona la logica aplicar en concatenacion de mismo campo
   if (isset($_GET["logica"])) $logica = $_GET["logica"];
   else $logica = 'OR';
   
   // creamos condiciones de busqueda para cada campo distinto
   for ($i=1; $i< $public_num_campos+1; $i++)
   {
       if (strlen($_GET["campo$i"])>0)
       {
         $nombre_campo = $_GET["campo$i"];
         // añade a la cadena de conciones para el campo el valor
         if (array_key_exists($nombre_campo, $campos))
         {
           $campos[$nombre_campo] .= $logica." ( valor LIKE '%".
            $_GET["valor$i"]."%' ".$lista_sinonimos[$i].')';
         }
         else
         {
           $campos[$nombre_campo] = "( valor LIKE '%".$_GET["valor$i"]."%' ".
            $lista_sinonimos[$i].')';
         }
       }
   }   
   
   // chequea si existe el campo auxiliar e insertalo
   if (isset($_GET["campo_aux"]) && (strlen($_GET["campo_aux"])>0) ) 
   {
         $nombre_campo = $_GET["campo_aux"];
         // añade a la cadena de conciones para el campo el valor
         if (array_key_exists($nombre_campo, $campos))
         {
           $campos[$nombre_campo] .= $logica.
                                      " valor LIKE '%".$_GET["valor_aux"]."%'";
         }
         else
         {
           $campos[$nombre_campo] = " valor LIKE '%".$_GET["valor_aux"]."%' ";
         }
   }  
         
   // inicializa variables
   $condicion_campos = "";
   $logica_campos = "";
   
   foreach ($campos as $clave => $cadena_valor)
   {
      if ($clave != 'ANY') // si el campo viene fijado
      {
       	$condicion_campos .= $logica_campos."(campo='".$clave."' AND".
       	                     " (".$cadena_valor."))";
      }
      else  // si no viene fijado
         {$condicion_campos .= $logica_campos." (".$cadena_valor.")";}
       	$logica_campos = " OR ";   
   }
   
   if (strlen($condicion_campos)>0)
   {
       $condicion_campos = "AND (".$condicion_campos.") ";
   }
   
   // devuelve el valor de condición
   return $condicion_campos;
}

//------------------------------------------------------------------
// Funcion: AUX_condicion_tipos
//
//  Esta funcion construye la consulta SQL de concicion para el
//  tipo de publicacion Web seleccionado
//
function AUX_condicion_tipos($tipo_libro)
{
  // inicializacion de Common
  global $public_rel_tipos;	
  // inicializacion de cadena
  $condicion_busqueda = "";	
	
  if (strlen($tipo_libro)>0)
  {  
     $condicion_tipos = "";
     $logica_tipos = "";
     foreach ($public_rel_tipos[$tipo_libro] as $tipo_bibtex)
     {
     	$condicion_tipos .= $logica_tipos."tipo='".$tipo_bibtex."'";
     	$logica_tipos = " OR ";
     } 
     $condicion_busqueda .= " (".$condicion_tipos.") ";
  }
  return $condicion_busqueda;	
}

//------------------------------------------------------------------
// Funcion: AUX_formatea_autor
//
//  Esta funcion formatea el campo author, separando cada autor
//  del siguiente mediante una coma, y poniendo el nombre delante
//  del apellido en aquellos autores que esten puestos al reves
//
// 
function AUX_formatea_autor($cadena_autores, $separador_campos)
{
  // inicializa variables
  $autores_final = '';
  $separador = $separador_campos;  
  
  // divide el campo autor entre sus autores
  $autores = explode(" and ",$cadena_autores);
  // si hay mas de uno, separalos por comas
  if (count($autores)>1)
  {
    // sustituye todos los And menos el último por una coma.
    for (end($autores);$autor=current($autores);prev($autores))
    {
        // si el campo tiene una coma, cambia el orden
        $comma = strpos($autor,',');
        if ($comma)
        {
          $autor = substr($autor,$comma+1,strlen($autor))." ". // nombre
                   substr($autor,0,$comma);                  // apellido
        }
        // añadelo al campo autor
        $autores_final = $separador.$autor.$autores_final;
        // calcula separador para proximo
        if ($autor == $autores[1]) $separador = "";
        else $separador = ", ";
    }
  } 
  else
  {
     // si el campo tiene una coma, cambia el orden
     $comma = strpos($cadena_autores,',');
     if ($comma)
     {
       $autores[0] = 
         substr($autores[0],$comma+1,strlen($autores[0]))." ". // nombre
         substr($autores[0],0,$comma);                  // apellido
     }

     $autores_final = $autores[0];
  }  
  // devuelve el valor de autores formateado
  return $autores_final;
} 

//--------------------------------------------------------------------------
// Funcion : AUX_sustitucion_sinonimos
// Descripcion:
//   Esta función busca y sustituye los sinonimos que se le pasan en el
//   array dentro del valor del campo.
// Parametros:
//   sinonimos : Entrada : Array de sinonimos a buscar
//   campo     : Entrada/Salida : La cadena a modificar.
//--------------------------------------------------------------------------
function AUX_sustitucion_sinonimos($sinonimos, &$campo)
{
   $campo_local = $campo;
   $cambiar_almohadilla = 0;
   
   // partimos el campo en separadores para extraer palabras
   $palabra = strtok($campo_local," {}");
   
   while ($palabra)
   {
   	// buscala en los sinonimos.
   	if ( array_key_exists($palabra,$sinonimos))
   	{
   	  $campo = str_replace($palabra, $sinonimos[$palabra], $campo);
   	  $cambiar_almohadilla = 1;
      }
      // pasa a la siguiente palabra
      $palabra = strtok(" {}");
   } 
   
   // sustituye almohadilla
   if ($cambiar_almohadilla)
   {
      $campo = str_replace("#", "", $campo);
   }
}

//------------------------------------------------------------------
// Funcion: AUX_campos_formateados
//
//  Esta funcion formatea una lista de campos BIBTEX de acuerdo
//  a un orden preestablecido. El orden será el definido en common.php:
//
function AUX_campos_formateados($campos_public, $campo_auxiliar,
                                $separador_campos)
{
  // inicializacion de variable
  $cadena_formateada = "";
  $separador = "";

  // definicion de Commons usados
  global $public_orden_campos;
  global $public_marcacion_campos;
  global $public_nombre_meses;
     
  for (reset($public_orden_campos);
       $campo = current($public_orden_campos);
       next($public_orden_campos))
  {
    // anexa campo si existe
    if (array_key_exists($campo, $campos_public))
    {
      // chequea si campo es autor y formatealo
      if ($campo == 'author')
      {
        $campos_public['author'] = AUX_formatea_autor(
                                   $campos_public['author'], $separador_campos);

      // Comprobar si Month contiene un literal
      } else if ($campo == 'month') {

	if (strpos($campos_public['month'], '{') === false 
		&& strpos($campos_public['month'], '}') === false ) {
	    $campos_public['month'] = $public_nombre_meses[$campos_public['month']];
	}

      }
      if (strstr($campos_public[$campo],"\\bf"))
      {
        $campos_public[$campo] = str_replace("\\bf",'',$campos_public[$campo]);
      }

      //añade prefijos y sufijos de campo
      $cadena_formateada .= $separador.
                            $public_marcacion_campos[$campo]['prefijo'].
                            $campos_public[$campo].
                            $public_marcacion_campos[$campo]['sufijo'];

      //inicializa separador y sufijo
      $separador = ', ';
    }
  }
  
  // añade campo auxiliar si existe
  if (strlen($campo_auxiliar)>0)
  {
    $cadena_formateada .= ".<br><I><B>".$campo_auxiliar."</B>=".
                          $campos_public[$campo_auxiliar]."</I>";
  }

  // sustituye las llaves de entrada y salida
  $cadena_formateada = str_replace("{","",$cadena_formateada);
  $cadena_formateada = str_replace("}","",$cadena_formateada);  
  $cadena_formateada = stripslashes($cadena_formateada);
  
  // devuelve cadena formateada
  return $cadena_formateada;
}


?>
