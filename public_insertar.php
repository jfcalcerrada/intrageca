<?
// ADVERTENCIA!!!
// requiere el include de los siguientes paquetes de un padre
// - config.php
// - common_pub.php
include "bibtex/inserta_BD_referencias.php";


//--------------------------------------------------------------------------
//  Funcion AUX_campo_renombrado 
//
//  Descripcion: Cambia el nombre de un campo de OPT<->nominal
//
function AUX_campo_renombrado($campo)
{
   if ((strpos($campo,'OPT')==0) AND !(strpos($campo,'OPT')=== false)) {
     $campo_modificado = substr($campo,3);
   } else {
     $campo_modificado = "OPT".$campo;
   }

   return $campo_modificado; 
}

//--------------------------------------------------------------------------
//  Funcion AUX_verifica_campos_especiales 
//
//  Descripcion: Verifica si el campo es uno de los especiales y lo asigna
//   a su correspondiente variable. Los campos especiales son:
//
//   year       -> fecha
//   month      -> fecha
//   OPTtipopub -> tipo
//
function AUX_verifica_campos_especiales($campo, $valor, &$fecha, &$tipo)
{
  global $public_rel_tipos;
  // verifica si es mes o año para guardar los valores
  if (($campo == 'year')||($campo == 'month')) {
     $fecha[$campo] = addslashes($valor);
  }
  // si es el nuevo campo es OPTtipopub sobreescribe tipo de busqueda
  if ($campo == 'OPTtipopub') {
     // chequea que el valor está dentro de los permitidos
     $tipo_formateado = trim(strtoupper($valor));
     
     for (reset($public_rel_tipos);
          $tipo_web = key($public_rel_tipos);
          next($public_rel_tipos))
     {
        if (in_array($tipo_formateado,$public_rel_tipos[$tipo_web])) {
          $tipo = $tipo_formateado;
        }
     }     
  } 
}


//--------------------------------------------------------------------------
// public_insertar.php
//
// Esta funcion actualiza los valores de un registro bibliografico
// en la base de datos.
// 
// Los parámetros de entrada de la función son:
//  $conexion : El manejador de la conexion a la base de datos
//  $registro : Array con los valores a actualizar. Estos son:
//   - id_ref : Numero de referencia. Si es 0, el registro es nuevo.
//   - id_ref_bibtex : Referencia bibtex a actualizar
//   - tipo : Tipo de publicacion bibtex
//   - estado : Estado de la publicacion
//   - publicar : Booleano que indica si es visible publicamente
//   - idioma : Codigo de idioma de la publicacion
//   - tipo_link : (E,I,N) que nos dice si es externo, interno o No disponible.
//   - link_refer : URL del documento
//
//   - numero_campos : El número de campos de la publicacion
//   - id_campo_c_$i : el nombre del campo
//   - id_campo_b_$i : Indica si se debe borrar el campo
//   - id_campo_m_$i : Indica si se debe cambiar el campo entre OPT 
//   - id_campo_t_$i : El texto a actualizar
//   - nuevo_campo   : el campo nuevo a insertar
//   - nuevo_valor   : el texto del nuevo campo
//
//  Devuelve el identificador del registro insertado/actualizado
//--------------------------------------------------------------------------

function public_insertar($conexion, $registro)
{  
  // definicion de globales
  global $public_tipo_links; 
  global $public_dir_docs;
  global $_FILES;

  //--------------------------------------------------------------------
  // COPIA/BORRA DOCUMENTO SI ESTÁ DEFINIDO
  //--------------------------------------------------------------------
  // fija link y nombre de documento
  $nombre_fichero = $public_dir_docs.$registro['id_ref_bibtex'].'.pdf';
  
  // verifica si insertamos uno nuevo
  if (isset($_FILES['fichero_pub']) && strlen($_FILES['fichero_pub']['name'])>0) {
   // admite solo pdfs
   if ($_FILES['fichero_pub']['type']=='application/pdf') {
     // copia fichero a directorio de curriculum renombrandolo
     copy($_FILES['fichero_pub']['tmp_name'],$nombre_fichero);
   }  
  }
  // verifica si existe interno y lo borramos si procede
  if (($registro['tipo_link'] != $public_tipo_links['Interno']) &&
     (file_exists($nombre_fichero)))
  {
    unlink($nombre_fichero);
  }
  //---------------------------------------------
  // Actualiza valores de la referencia
  //---------------------------------------------
  // prepara datos para insertar desde web
  $id_ref_bibtex = addslashes($registro['id_ref_bibtex']);
  if ($registro['tipo_link'] == $public_tipo_links['Externo']) {
      $link_refer = addslashes($registro['link_refer']);
    }
  else if (($registro['tipo_link'] == $public_tipo_links['Interno'])&&
           (file_exists($nombre_fichero)))
    {
      $link_doc_interno = 'docs/'.$registro['id_ref_bibtex'].'.pdf'; 
      $link_refer = $link_doc_interno;
    }  
  $publicar = (AUX_estado_es_visible($registro['estado']) == 1)?  1:0;
  
  // chequea si hay que insertar un nuevo registro o solo actualizarlo
  if ($registro['id_ref'] == 0) {
     // construye la consulta de Insercion
     $consulta_public ='INSERT INTO referencias(id_ref_bibtex, tipo, visible,'.
       ' tipo_link, link_referencia, idioma, estado, tipo_bibtex) VALUES("'.
       $id_ref_bibtex.'","'.$registro['tipo'].'", '.$publicar.', "'.
       $registro['tipo_link'].'", "'.$link_refer.'","'.$registro['idioma'].
       '","'.$registro['estado'].'", "'.$registro['tipo'].'")';
  }
  else {
     // construye la consulta de actualizacion
     $consulta_public = 'UPDATE referencias SET id_ref_bibtex="'.
       $id_ref_bibtex.'", tipo="'.$registro['tipo'].'", tipo_bibtex="'.
       $registro['tipo'].'", visible='.$publicar.', tipo_link="'.
       $registro['tipo_link'].'", link_referencia="'.$link_refer.
       '", idioma="'.$registro['idioma'].'", estado="'.$registro['estado']
       .'" WHERE id_referencia='.$registro['id_ref'];
  }
  
  // realiza consulta de miembro
  $resultado = mysql_query($consulta_public, $conexion);

  // si da error, devuelve 0
  if (!$resultado) {
   echo "Error de consulta ".$consulta_public;
   return 0;
  }

  // obtiene el valor del elemento insertado/actualizado
  if ($registro['id_ref'] == 0) {
     $id_referencia = mysql_insert_id(); 
     
     // inserta una referencia de campo nueva
     $consulta_id = 'SELECT MAX(id_campos) FROM ref_relacion';
     $resultado =  mysql_query($consulta_id, $conexion);
     $identificador_tmp = mysql_fetch_row($resultado);
     $id_campo = $identificador_tmp[0] + 1;
     
     $consulta_cross = 'INSERT INTO ref_relacion(id_ref,'.
      'id_campos, referencia_cruzada, id_ref_cruzada) VALUES('.
      $id_referencia.','.$id_campo.',0,0)';
      
     $resultado =  mysql_query($consulta_cross, $conexion);
     
     if (! $resultado) {
      echo "Error de consulta ".$consulta_cross;
      return 0;           
     }         
  }  
  else {
     $id_referencia = $registro['id_ref'];
     
     // obtiene el id_campos para el registro actualizado
     $consulta_id = 'SELECT id_campos FROM ref_relacion '.
      'WHERE referencia_cruzada=0 AND id_ref='.$id_referencia;
     $resultado =  mysql_query($consulta_id, $conexion);
     $identificador_tmp = mysql_fetch_row($resultado);
     $id_campo = $identificador_tmp[0];     
  }
  
  //---------------------------------------------  
  // Actualización de los campos de la referencia
  //---------------------------------------------
  // variables de fecha
  $fecha_publicacion = array();
  // variable de sobreescritura de tipo de busqueda
  $tipo_busqueda = $registro['tipo'];
  // array de campos
  $lista_campos = array();

  // insertar toda la informacion de los campos en un array
  for ($i=1; $i<= $registro['numero_campos'];$i++) {
    $campo = $registro["id_campo_c_$i"];
    $lista_campos[$campo]['borrar'] = $registro["id_campo_b_$i"];
    $lista_campos[$campo]['modificar'] = $registro["id_campo_m_$i"];
    $lista_campos[$campo]['valor'] = addslashes($registro["id_campo_t_$i"]);
  }
  
  // recorremos lista borrando todos los marcados como 'borrar' o 'modificar'
  // y actualizando el valor del resto
  foreach ($lista_campos as $campo => $valor) {
    if ($lista_campos[$campo]['borrar'] == 1) {
       // si campo es crossref, borramos el enlace
       if ($campo == 'crossref') {
          Borra_Crossref($id_referencia, $conexion);
       }
       
       // construye consulta de borrado
       $consulta_campo='DELETE FROM ref_campos WHERE id_campo_ref='.
         $id_campo.' AND campo="'.$campo.'"';
             
    } elseif ($lista_campos[$campo]['modificar'] == 1) {
        $campo_modificado = AUX_campo_renombrado($campo);

        // realiza consulta de borrado de las dos posibilidades de campo
        // !! si eres torpe y no te das cuenta, adios a uno de los campos !!!
        $consulta_campo='DELETE FROM ref_campos WHERE id_campo_ref='.
         $id_campo.' AND (campo="'.$campo.'" OR '.'campo="'.
         $campo_modificado.'")';
    } else {
       // si campo es crossref, actualizamos su valor
       if ($campo == 'crossref') {
          Borra_Crossref($id_referencia, $conexion);
          Inserta_Crossref($lista_campos[$campo]['valor'], $id_referencia, $conexion);
       }
       // construye consulta de update
       $consulta_campo='UPDATE ref_campos SET valor="'.
         $lista_campos[$campo]['valor'].'" WHERE id_campo_ref='.$id_campo.
         ' AND campo="'.$campo.'"';
      
       AUX_verifica_campos_especiales($campo, $lista_campos[$campo]['valor'],
          $fecha_publicacion, $tipo_busqueda);             
    }

    // ejecuta consulta para uno de los cuatro casos posibles
    $resultado = mysql_query($consulta_campo, $conexion);
      
    // si da error, saca un mensaje
    if (!$resultado) {
         echo "No se pudo ejecutar la consulta ".$consulta_campo;
    }    
  }  
  
  // por ultimo, recorre una ultima vez la lista para insertar
  // con el nuevo nombre aquellos campos que hayan sido renombrados
  foreach ($lista_campos as $campo => $valor) {
    if ($lista_campos[$campo]['modificar'] == 1) {
      $campo_modificado = AUX_campo_renombrado($campo); 
      
      // Verificamos gestion de crossref
      if ($campo_modificado == 'crossref') {
        Inserta_Crossref($lista_campos[$campo]['valor'], $id_referencia, $conexion);

      } else if ($campo_modificado == 'OPTcrossref') {
         Borra_Crossref($id_referencia, $conexion);
      }

      // construye consulta de insercion     
      $consulta_campo='INSERT INTO ref_campos(id_campo_ref, campo, valor) '.
         'VALUES ('.$id_campo.',"'.$campo_modificado.'","'.
         $lista_campos[$campo]['valor'].'")'; 
      // ejecuta consulta para uno de los tres casos posibles
      $resultado = mysql_query($consulta_campo, $conexion);
      
      // si da error, saca un mensaje
      if (!$resultado) {
         echo "No se pudo ejecutar la consulta ".$consulta_campo;
      }

       AUX_verifica_campos_especiales($campo_modificado, 
          $lista_campos[$campo]['valor'],$fecha_publicacion, $tipo_busqueda);          
    } 
  }  

  //------------------------------------------------------------  
  // inserta el nuevo campo si no existe ya y no está vacío.
  // Esto se tiene que hacer despues de haber procesado los demas
  // para poder chequear que no existe ya el nombre a insertar
  //------------------------------------------------------------
  
  if (strlen($registro["nuevo_campo"]) > 0) {
     $campo_existe = false;
     $nuevo_campo = $registro["nuevo_campo"];
     $campo_modificado = AUX_campo_renombrado($nuevo_campo);   
           
     // verifica que campo no existe
     if ((array_key_exists($nuevo_campo,$lista_campos)) AND 
        ($lista_campos["$nuevo_campo"]['modificar'] == 0) AND
        ($lista_campos["$nuevo_campo"]['borrar'] == 0))
      { $campo_existe = true;}
     
     // verifica que no se ha renombrado uno existente con nombre final igual
     if ((array_key_exists($campo_modificado,$lista_campos)) AND 
        ($lista_campos["$campo_modificado"]['modificar'] == 1))
     { $campo_existe = true;}
     
     // verifica que no es uno prohibido
     if (($nuevo_campo == 'OPTidioma')||($nuevo_campo == 'OPTestado'))
     { $campo_existe = true; }
     
     if (! $campo_existe)
     {
       // prepara valores
       $campo_nuevo = addslashes($registro["nuevo_campo"]);
       $valor_nuevo = addslashes($registro["nuevo_valor"]);

       // Verificamos gestion de crossref
       if ($campo_nuevo == 'crossref')
       {
         Inserta_Crossref($valor_nuevo, $id_referencia, $conexion);
       }

       AUX_verifica_campos_especiales($campo_nuevo, $valor_nuevo,
          $fecha_publicacion, $tipo_busqueda);
       
       // construye consulta
       $consulta_insertar = 'INSERT INTO ref_campos(id_campo_ref, campo, valor) '.
         'VALUES ('.$id_campo.',"'.$campo_nuevo.'","'.$valor_nuevo.'")';
       // ejecuta consulta   
       $resultado = mysql_query($consulta_insertar, $conexion);  
       
       // si da error, saca un mensaje
       if (!$resultado)
       {
           echo "No se pudo ejecutar la consulta ".$consulta_insertar;
       }
     }
  }

  //------------------------------------------------------------------------  
  // Comprobación. En caso que NO HAYA DEFINIDO NINGUN CAMPO. Se inserta uno
  // Dummy Author para poder acceder al registro desde el web.
  //------------------------------------------------------------------------
  else if ($registro['numero_campos'] == 0)
  {
     // construye consulta
     $consulta_insertar = 'INSERT INTO ref_campos(id_campo_ref, campo, valor) '.
       'VALUES ('.$id_campo.',"author","Sin Definir")';
       
     // ejecuta consulta   
     $resultado = mysql_query($consulta_insertar, $conexion);
     
     // si da error, saca un mensaje
     if (!$resultado)
     {
         echo "No se pudo ejecutar la consulta ".$consulta_insertar;
     }       
  }

  //----------------------------------------------------------------------  
  // Por ultimo, actualizamos la fecha de la referencia y tipo de busqueda
  //----------------------------------------------------------------------  
  // calcula la nueva fecha
  $nueva_fecha = Calcula_fecha_publicacion($fecha_publicacion);
  
  // actualiza fecha con crossref si no tiene
  if ($nueva_fecha == "'9999-01-01'")
  {
     $consulta_crossref = 'SELECT fecha_publicacion '.
      'FROM ref_relacion LEFT JOIN referencias '.
      'ON referencias.id_referencia=ref_relacion.id_ref_cruzada '.
      'WHERE referencia_cruzada=1 AND id_ref='.$id_referencia;
     
     $resultado = mysql_query($consulta_crossref, $conexion);
     
     if (mysql_num_rows($resultado) > 0)
     {
       $fecha_crossref = mysql_fetch_row($resultado);
       $nueva_fecha = $fecha_crossref[0];
     }
  }
  
  // construye consulta de actualizacion
  $consulta_fecha = 'UPDATE referencias SET fecha_publicacion='.$nueva_fecha.
    ', tipo="'.$tipo_busqueda.'" WHERE id_referencia='.$id_referencia;

  // ejecuta consulta   
  $resultado = mysql_query($consulta_fecha, $conexion);
     
  // si da error, saca un mensaje
  if (!$resultado)
  {
      echo "No se pudo ejecutar la consulta ".$consulta_fecha;
  } 
          
  // devuelve el valor del elemento insertado
  return $id_referencia;
} 
