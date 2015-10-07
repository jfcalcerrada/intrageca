<?
// inclusion de funciones de Bibtex
include "funciones_bibtex.php";

//--------------------------------------------------------------------------
// Funcion : lectura_registro
// Descripcion:
//   Esta función lee un registro completo que se le pasa en una cadena
//   y lo almacena en una estructura de salida
// Parametros:
//   registro : Entrada : El registro a leer
//   bibliografia : Salida  : Estructura que almacena los datos del registro
//   lista_sinonimos: Entrada/Salida : La lista de sinonimos que es aplicable
// Devuelve:
//   False si fallo en leer el registro o TRUE si lo leyo bien
//--------------------------------------------------------------------------
function lectura_registro($registro, &$bibliografia, &$lista_sinonimos)
{
   // extrae el tipo de registro BIBTEX
   $tipo_registro = BIBTex_extrae_tipo_registro($registro);
      
   if (!$tipo_registro)
   { 
      // si no encuentra tipo de registro, no devuelve nada
      return FALSE; 
   }
   else
   {
        switch ($tipo_registro) 
        {
          case "STRING"  :        // Inserta en lista de sinonimos
               list($clave,$valor) = BIBTex_extrae_sinonimo($registro);
               BIBTex_sustitucion_acentos($valor);
               $lista_sinonimos[$clave] = $valor; 
               break; 
               
          case "PREAMBLE": break; // No hacemos nada
          
          case "COMMENT" : break; // No hacemos nada 
                
          default:                

               // extrae identificador y campos
               $identificador  = BIBTex_extrae_identificador($registro);
               $campos         = BIBTex_extrae_campos($registro);

               // si los campos no están bien, no devuelve registro
               if (($campos == FALSE) || (strlen($identificador)== 0))
                 return FALSE;
               // sustituye los acentos en cada uno de los campos
               for(reset($campos); $clave = key($campos); next($campos))
               {
                 BIBTex_sustitucion_acentos($campos[$clave]);
               }
               
               // Inserta referencia en bibliografía
               $bibliografia[$tipo_registro][$identificador] = $campos;
            break;
        }  
   }
   return TRUE;
}

//--------------------------------------------------------------------------
// Funcion : lectura_fichero_bibtex
// Descripcion:
//   Esta función lee un fichero bibtex que se le pasa como parámetro
//   almacenando en la estructura de retorno toda la información contenida
//   en dicho fichero devidamente estructurada.
// Parametros:
//   nombre_fichero : Entrada : El nombre del fichero a leer
//   datos_fichero  : Salida  : Estructura que almacena los datos del fichero
//   error_log          : Salida  : Mensaje de error en caso de fallo de lectura
// Devuelve:
//    TRUE si encontró el fichero y FALSE si no lo encontro
//--------------------------------------------------------------------------
function lectura_fichero_bibtex($nombre_fichero, &$datos_fichero, &$error_log)
{
  
  // inicialización de variables
  $numero_linea     = 0;
  $lista_sinonimos  = array();
  $bibliografia     = array();
  
  // abre fichero de bibliografía
  $acceso_fichero = fopen($nombre_fichero,"r");
  
  // verificamos si encontró fichero
  if (! $acceso_fichero)
  {
    $error_log = "Fichero no encontrado\n";
    return FALSE;
  }
  
  // recorre todo el fichero
  while (!feof($acceso_fichero)) 
  {
    $linea = fgets($acceso_fichero, 4096);
    $numero_linea++;
    
    // elimina espacios sobrantes de cadena
    $linea = trim($linea);
    
    // Busca nuevo registro. Para ello verifica primera linea con @
    if (strstr($linea,'@'))
    {
      // concatena todo el registro hasta el fin de llaves del mismo
      // para ello, va añadiendo lineas y contando las llaves de
      // apertura y cierre, hasta que su número coincida y sean distintas de 0
      $registro            = $linea;
      $num_llaves_apertura = substr_count($registro,'{');
      $num_llaves_cierre   = substr_count($registro,'}');
      
      // bucle de concatenación de registro
      while ((($num_llaves_apertura == 0) || // evita estado inicial
              ($num_llaves_apertura != $num_llaves_cierre)) &&
             (!feof($acceso_fichero))) // si llega a final de fichero sale de bucle
      { 
        // lee nueva linea y limpia espacios
        $linea = fgets($acceso_fichero, 4096);
        $numero_linea++;
        $linea = trim($linea);
        
        $registro .= ' ' . $linea; // añade la nueva linea
        $num_llaves_apertura = substr_count($registro,'{');
        $num_llaves_cierre   = substr_count($registro,'}');
      }
      // lee registro e insertalo en bibliografia o lista de sinonimos
      $leido = lectura_registro($registro, $bibliografia, $lista_sinonimos);
      
      // verifica si ha habido algún problema
      if ($leido==FALSE)
      {
         $error_log = "Fallo al analizar registro : ".$registro." \n";
         return FALSE;
      }
    }
  }

  // cierra fichero
  fclose($acceso_fichero);
  
  // asigna datos de bibliografia y sinomimos
  $datos_fichero = array ( 'bib' => $bibliografia,
                          'sin' => $lista_sinonimos);
 
  return TRUE;
}

//--------------------------------------------------------------------------
// Funcion : lectura_texto_bibtex
// Descripcion:
//   Esta función lee un texto bibtex que se le pasa como parámetro
//   almacenando en la estructura de retorno toda la información contenida
//   en dicho fichero devidamente estructurada.
// Parametros:
//   texto_bibtex : Entrada : El texto a analizar
//   datos_bibtex  : Salida : Estructura que almacena los datos del texto
//   error_log    : Salida  : Mensaje de indicacion de error
// Devuelve
//   false no encontró ningún registro valido
//--------------------------------------------------------------------------
function lectura_texto_bibtex($texto_bibtex, &$datos_bibtex, &$error_log)
{
 // inicializacion de variables
 $texto_local = $texto_bibtex;
 $num_llaves_apertura = 0;
 $num_llaves_cierre   = 0;
 $lista_sinonimos  = array();
 $bibliografia     = array();
 
 // parte el texto en registros, separados por @
 $inicio = strpos($texto_local, '@');
 $fin    = strlen($texto_local);
 $puntero = $inicio;

 // verifica si hay el inicio de un registro
 if ($inicio === FALSE)
 {
   $error_log = "No se pudo encontrar inicio de registro Bibtex\n";
   return FALSE;
 }
 
 // para cada registro
 while ($inicio < $fin)
 {
   $puntero ++;
   
   // cuenta las llaves
   if (substr($texto_local,$puntero,1) == '{') $num_llaves_apertura++;
   else if (substr($texto_local,$puntero,1) == '}') $num_llaves_cierre++;
   // si encuentra inicio de otro registro, almacena actual
   else if (((substr($texto_local,$puntero,1) == '@') && 
            ($num_llaves_apertura == $num_llaves_cierre)) ||
            ($puntero == $fin))
   {
      // captura registro completo
      $registro = substr($texto_local,$inicio,$puntero-$inicio);

      // lee registro e insertalo en bibliografia o lista de sinonimos
      $leido = lectura_registro($registro, $bibliografia, $lista_sinonimos);

      // verifica si ha habido algún problema
      if ($leido==FALSE)
      {
         $error_log = "Fallo al analizar registro : ".$registro." \n";
         return FALSE;
      }
      
      // reposiciona inicio
      $inicio = $puntero; 
   }
 }
 
  // asigna datos de bibliografia y sinomimos
  $datos_bibtex = array ( 'bib' => $bibliografia,
                          'sin' => $lista_sinonimos);
 
 return TRUE;
}
?>