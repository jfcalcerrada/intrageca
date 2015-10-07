<?

// declaración de array de Tipos de Registros BIBTEX
//   Comandos: STRING, PREAMBLE, COMMENT
//   Tipos   : ARTICLE, BOOK, BOOKLET, CONFERENCE, INBOOK, INCOLLECTION,
//             INPROCEEDINGS, MANUAL, MASTERSTHESIS, MISC, PHDTHESIS,
//             PROCEEDINGS, TECHREPORT, UNPUBLISHED
//   Tipos adicionales : COLLECTION, PATENT
$BIBTex_tipos_registro = array ( "STRING", "PREAMBLE", "COMMENT",
   "ARTICLE", "BOOK", "BOOKLET", "CONFERENCE", "INBOOK", "INCOLLECTION",
   "INPROCEEDINGS", "MANUAL", "MASTERSTHESIS", "MISC", "PHDTHESIS",
   "PROCEEDINGS", "TECHREPORT", "UNPUBLISHED", "COLLECTION", "PATENT");
   
//--------------------------------------------------------------------------   
// Funcion : BIBTex_extrae_tipo_registro
// Descripcion:
//   Esta función lee un registro completo y extrae su tipo
// Parametros:
//   registro   : Entrada : El registro que debe analizarse
// Devuelve:  
//   El tipo de registro según BIBTeX. Los tipos de registro estandar son:
//--------------------------------------------------------------------------
function BIBTex_extrae_tipo_registro($registro)
{   
  // importamos array de tipos de registro
  global $BIBTex_tipos_registro; 
   
  // extraemos cadena de tipo
  $registro_local = $registro;
  $tipo_registro = strtok($registro_local,"@{ ");
  
  // convertimos a mayusculas el tipo
  $tipo_registro = strtoupper($tipo_registro);

  // buscamos en array de tipos definidos
  if (in_array($tipo_registro,$BIBTex_tipos_registro))
  {
   return $tipo_registro;
  }
  else {
   return FALSE;
  } 
}

//--------------------------------------------------------------------------
// Funcion : BIBTex_extrae_sinonimo
// Descripcion:
//   Esta función lee un registro de tipo STRING y extrae el sinonimo
// Parametros:
//   registro   : Entrada : El registro que debe analizarse
// Devuelve:  
//   Un array de dos elementos, con el identificador y el sinónimo asociado
//--------------------------------------------------------------------------
function BIBTex_extrae_sinonimo($registro)
{
  // quitamos espacios por delante y detras
  $registro_local = trim($registro);
  
  // obtenemos acronimo
  strtok($registro_local,"@{ =");
  $acronimo = strtok("@{ =");
  
  //obtiene sinonimo
  $inicio_sinonimo = strpos($registro_local,"=")  + 1;
  $fin_sinonimo    = strrpos($registro_local,"}");
  $longitud        = $fin_sinonimo - $inicio_sinonimo;
  $sinonimo        = substr($registro_local, $inicio_sinonimo, $longitud); 
  
  // devolvemos los valores
  return array($acronimo,$sinonimo);
}

//--------------------------------------------------------------------------
// Funcion : BIBTex_extrae_identificador
// Descripcion:
//   Esta función lee un registro de tipo STRING y extrae el identificador
// Parametros:
//   registro   : Entrada : El registro que debe analizarse
// Devuelve:  
//   Una cadena de caracteres con el identificador del registro
//--------------------------------------------------------------------------
function BIBTex_extrae_identificador($registro)
{
  // quitamos espacios por delante y detras
  $registro_local = trim($registro);
  
  // obtenemos acronimo
  strtok($registro_local,"@{ ");
  $identificador = strtok(","); 
  
  // aislamos palabra sin espacios
  $identificador = trim($identificador);
  
  // devolvemos identificador
  return $identificador;  
}

//--------------------------------------------------------------------------
// Funcion : BIBTex_extrae_campos
// Descripcion:
//   Esta función lee un registro de tipo STRING y extrae los campos de
//   dicho registro
// Parametros:
//   registro   : Entrada : El registro que debe analizarse
// Devuelve:  
//   Un array formado por los campos y su valor que tiene asociado el
//   registro.
//--------------------------------------------------------------------------
function BIBTex_extrae_campos($registro)
{
  // crea arrays de campos
  $campos       = array();
    
  // quitamos espacios por delante y detras
  $registro_local = trim($registro);

  // calculamos inicio y fin de campos
  $inicio_campos = strpos($registro_local,",")  + 1; 
  $fin_campos    = strrpos($registro_local,"}");
  
  // extrae cadena de campos del registro
  $longitud = $fin_campos - $inicio_campos;
  $cadena_campos = substr($registro_local, $inicio_campos, $longitud);
   
  // Para separar los campos, recorremos la cadena,
  // buscando los separadores de '=' y ','.
  $clave_campo = trim(strtok($cadena_campos,"="));
  
  while ($clave_campo)
  {
    // inicializa valor de campo
    $valor_campo = "";
    $num_llaves_abre = 0;
    $num_llaves_cierre = 1;
    
    // busca la siguiente coma que no este dentro de llave
    while ($num_llaves_abre != $num_llaves_cierre)
    {
      $valor_anadir = strtok(",");
      // si no lo encuentra, devuelve error
      if (strlen($valor_anadir)== 0) return FALSE;
      // sino, añadelo y sigue contando
      $valor_campo.= $valor_anadir;
      $num_llaves_abre = substr_count($valor_campo,'{');
      $num_llaves_cierre = substr_count($valor_campo,'}');
    }
    // asigna valor y clave
    $campos[$clave_campo] = trim($valor_campo);
    
    // ve a siguiente campo
    $clave_campo = trim(strtok("="));
  
  };
  
  // devuelve array asociativo
  return $campos;
}


//--------------------------------------------------------------------------
// Funcion : BIBTex_sustitucion_acentos
// Descripcion:
//   Esta función sustituye los acentos LATEX que pueda encontrar dentro
//   del array de valores que se le pasan por parámetro. Los acentos se
//   describen en LATEX de la siguiente forma:
//    \'{a}  = á  \'{e}  = é  \'{i}  = í  \'{o}  = ó   \'{u}  = ú
//    \'\a{} = á  \'\e{} = é  \'\i{} = í  \'\o{} = ó   \'\u{} = ú
// Parametros:
//   campo   : Entrada/Salida : La cadena a modificar.
//--------------------------------------------------------------------------
function BIBTex_sustitucion_acentos(&$campo)
{
   // define array de equivalencia
   $eq_acentuada = array ( 
      'a'=>'á', 'e'=>'é', 'i'=>'í', 'o'=>'ó', 'u'=>'ú',
      'A'=>'Á', 'E'=>'É', 'I'=>'Í', 'O'=>'Ó', 'U'=>'Ú');
   
   // busca el patron precedente de acentos de 1º tipo
   if (strstr($campo,'\\\'{'))
   { 
     // busca vocales acentuadas y sustituyelas   
     while (ereg("\\\'{([a,e,i,o,u,A,E,I,O,U]{1})}",$campo,$vocal))
     {
       // sustituyela
       $campo = str_replace($vocal[0], $eq_acentuada[$vocal[1]], $campo);
     }
     
     // sustituye otras posibles acentuaciones por su valor sin acentuar
     $campo = ereg_replace("\\\'{([a-z,á-ú,A-Z,Á-Ú]{1})}","\\1",$campo);
   } 
   // busca el patron precedente de acentos de 2º tipo
   if (strstr($campo,'\\\'\\'))
   { 
     // busca vocales acentuadas y sustituyelas  
     for (reset($eq_acentuada);$clave = key($eq_acentuada); next($eq_acentuada))
     {
       // sustituyela
       $campo = str_replace('\\\'\\'.$clave.'{}',$eq_acentuada[$clave], $campo);
     }
   }
}


//--------------------------------------------------------------------------
// Funcion : BIBTex_restitucion_acentos
// Descripcion:
//   Esta función restituye los acentos LATEX que pueda encontrar dentro
//   del array de valores que se le pasan por parámetro. Los acentos se
//   describen en LATEX de la siguiente forma:
//    \'{a}  = á  \'{e}  = é  \'{i}  = í  \'{o}  = ó   \'{u}  = ú
// Parametros:
//   campo   : Entrada : La cadena a modificar.
// Devuelve:
//   cadena con los acentos sustituidos
//--------------------------------------------------------------------------
function BIBTex_restitucion_acentos($campo)
{
   // define array de equivalencia
   $eq_acentuada = array ( 
      'á'=>"\\'{a}", 'é'=>"\\'{e}", 'í'=>"\\'{i}", 'ó'=>"\\'{o}", 
      'ú'=>"\\'{u}",
      'Á'=>"\\'{A}", 'É'=>"\\'{E}", 'Í'=>"\\'{I}", 'Ó'=>"\\'{O}", 
      'Ú'=>"\\'{U}");
   
   $campo_tmp = $campo;
   
   // busca vocales acentuadas y sustituyelas  
   for (reset($eq_acentuada);$clave = key($eq_acentuada); next($eq_acentuada))
   {
       // sustituyela
       $campo_tmp = str_replace($clave,$eq_acentuada[$clave], $campo_tmp);
   }
   
   return $campo_tmp;
}
?>