<?

//--------------------------------------------------------------------------
// Function: MBR_obtener_nombres_bibtex
//
// Esta función devuelve una cadena de busqueda para publicaciones con los
// campos de autor de un miembro determinado.
//
// Parametros de entrada
//   $id_miembro : La identidad del miembro a buscar.
//   $conexion   : La conexion a base de datos a emplear

function MBR_obtener_nombres_bibtex($id_miembro)
{
  // construye consulta para hayar publicaciones
  $consulta_pub = "SELECT texto_bibtex FROM miembro_bibtex WHERE id_miembro=".
                  $id_miembro;
  
  // realiza consulta de publicaciones
  $resultado = mysql_query($consulta_pub);
  $numero_filas = mysql_num_rows($resultado);
  
  // si hay alguna referencia Bibtex, pon el enlace a publicaciones
  if ($numero_filas > 0)
  {
     $i = 0;
     $cadena_parametros = "logica=OR&";
     // añade como campo cada uno de los parametros
     while ($id_bibtex=mysql_fetch_row($resultado))
     {
       $i++;
       $cadena_parametros.="campo".$i."=author&valor".$i."=".$id_bibtex[0]."&";
     }
     // añade el numero de campos
     $cadena_parametros .= "num_campos=".$i;
  } 
  
  // devuelve el valore de la cadena
  return $cadena_parametros; 
}    
?>