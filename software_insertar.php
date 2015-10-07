<?

//--------------------------------------------------------------------------
// software_insertar.php
//
// Esta funcion actualiza los valores de un software
// en la base de datos.
// 
// Los parámetros de entrada de la función son:
//  $conexion : El manejador de la conexion a la base de datos
//  $registro : Array con los valores a actualizar. Estos son:
//   - ids : Identificador de software. Si es 0, el registro es nuevo.
//   - idioma: El idioma en el que se insertan los datos
//   - titulo : el titulo del proyecto
//   - desc_corta: descripcion corta
//   - descripcion: textarea de descripcion
//   - publico: indica si está publicado o no
//   - sist_oper : sistema operativo
//   - licencia : Licencia del software
//   - link_licencia : link a página de licencia del software
//   - email: email de soporte 
//   - id_bibtex: campo OPT programa para referencias
//   - homepage: link a página principal del software
//
//  Devuelve el identificador del registro insertado/actualizado
//--------------------------------------------------------------------------

function software_insertar($conexion, $registro)
{  
  // directorio donde se crean los paquetes
  global $software_dir_paquetes;
  // lista de idiomas diponibles
  global $gen_idiomas_disp;
  
  //---------------------------------------------
  // Actualiza valores de la referencia
  //---------------------------------------------
  // prepara datos para insertar desde web
  $titulo = addslashes($registro['titulo']);
  $desc_corta =  addslashes($registro['desc_corta']);
  $descripcion = addslashes($registro['descripcion']);
  $sist_oper = addslashes($registro['sist_oper']);
  $licencia = addslashes($registro['licencia']);
  $link_licencia = addslashes($registro['link_licencia']);
  $email = addslashes($registro['email']);
  $id_bibtex =  addslashes($registro['id_bibtex']);
  $homepage = addslashes($registro['homepage']);
  $publicar = ($registro['publico'] == 1)? 1:0;
  
  // chequea si hay que insertar un nuevo registro o solo actualizarlo
  if ($registro['ids'] == 0)
  {
     // construye la consulta de Insercion
     $consulta_software ='INSERT INTO software( sistema_operativo, licencia,'.
       ' link_licencia, email_soporte, link_homepage, publico) VALUES("'.
       $sist_oper.'","'.$licencia.'","'.$link_licencia.'","'.$email.'","'.
       $homepage.'",'.$publicar.')';
  }
  else
  {
     // construye la consulta de actualizacion
     $consulta_software = 'UPDATE software SET '.
       ' id_sw_bibtex="'.$id_bibtex.'", sistema_operativo="'.
       $sist_oper.'", licencia="'.$licencia.'", link_licencia="'.
       $link_licencia.'", email_soporte="'.$email.'", link_homepage = "'.
       $homepage.'", publico='.$publicar.' WHERE id_software='.$registro['ids'];
  }
  
  // realiza consulta de miembro
  $resultado = mysql_query($consulta_software, $conexion);

  // si da error, devuelve 0
  if (!$resultado)
  {
   echo "Error en la consulta ".$consulta_software;
   return 0;
  }

  // obtiene el valor del elemento insertado/actualizado
  if ($registro['ids'] == 0)
  {
     $id_software = mysql_insert_id();
     // crea directorio nuevo para almacenar paquetes
     mkdir($software_dir_paquetes.'paq_'.$id_software,0777);
     
     // inserta registros en tabla de idiomas para el idioma de la intranet
     // (El primero de la lista)
     reset($gen_idiomas_disp);
     $idioma_def = key($gen_idiomas_disp);

     $consulta_idiomas = 'INSERT INTO software_idiomas(id_software, idioma'.
         ', titulo) VALUES('.$id_software.',"'.$idioma_def.'","SIN DEFINIR")';
       
     $resultado = mysql_query($consulta_idiomas, $conexion);  
     if (!$resultado)
     {
         echo "No se pudo ejecutar consulta ".$consulta_idiomas;
         return 0;      
     }   
     
  }  
  else
  {
     $id_software = $registro['ids'];
  }

  //---------------------------------------------
  // Actualiza valores dependientes de idioma
  //---------------------------------------------
  // verifica si registro ya está insertado
  $consulta = 'SELECT COUNT(*) FROM software_idiomas WHERE idioma="'.
         $registro['idioma'].'" AND id_software='.$id_software; 
 
  // ejecuta la consulta
  $resultado = mysql_query($consulta, $conexion);

  // si da error, devuelve 0
  if (!$resultado)
  {
   echo "Error en la consulta ".$consulta;
   return 0;
  }
  
  // verifica si ya hay uno insertado o no para insertarlo
  $ya_insertado = mysql_fetch_row($resultado);
  
  if (! $ya_insertado[0])
  {
     $consulta_idiomas = 'INSERT INTO software_idiomas(id_software, idioma,'.
         'titulo, descrip_corta, descripcion) VALUES('.$id_software.',"'.
         $registro['idioma'].'","'.$titulo.'", "'.$desc_corta.'", "'.
         $descripcion.'")';
  }
  else
  {
     $consulta_idiomas = 'UPDATE software_idiomas SET titulo="'.$titulo.
       '", descrip_corta="'.$desc_corta.'", descripcion="'.$descripcion.
       '" WHERE idioma="'.$registro['idioma'].'" AND id_software='.$id_software;
  }   

  // ejecuta la consulta
  $resultado = mysql_query($consulta_idiomas, $conexion);

  // si da error, devuelve 0
  if (!$resultado)
  {
   echo "Error en la consulta ".$consulta_idiomas;
  }
  
  
  return $id_software;

} 

?>