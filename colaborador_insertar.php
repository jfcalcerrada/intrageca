<?
//--------------------------------------------------------------------------
//  funcion de colaborador_insertar
//
//  esta funcion actualiza la informacion de los colaboradores que se
//  haya modificado.
//  Parametros:
//
//   gc_id_grupo   : El identificador del grupo
//   gc_nombre     : El nombre del grupo
//   gc_publico    : Indica si el grupo es publico o no
//   gc_desc       : La descripcion del grupo $i
//   gc_link       : El link del grupo
//   gc_num_mi     : El numero de miembros del grupo
//   mi_nuevo      : El nuevo miembro a insertar
//   mi_id_mie_$i  : El identificador del miembro $i del grupo
//   mi_borrar_$i  : Orden de borrar el miembro $i del grupo
//   mi_nombre_$i  : El nombre del miembro $i del grupo
//   mi_puesto_$i  : El puesto del miembro $i del grupo
//   mi_email_$i   : El email del miembro $i del grupo
//   mi_link_$i    : El link del miembro $i del grupo
//   mi_dir_$i     : Indica si es un director de grupo
//   
//   devuelve el identificador del grupo colaborador
//--------------------------------------------------------------------------
function colaborador_insertar($conexion, $registro)
{
  // prepara datos para insertar desde web
  $nombre = addslashes($registro['gc_nombre']);
  $descripcion = addslashes($registro['gc_desc']);
  $link = addslashes($registro['gc_link']);
  $publico = ($registro['gc_publico'] == 1)?  1:0;
  
  // chequea si hay que insertar un nuevo registro o solo actualizarlo
  if ($registro['gc_id_grupo'] == 0)
  {
    // construye la consulta de insercion de grupo colaborador
    $consulta_grupo = 'INSERT INTO grupos_colaboradores(nombre_grupo, '.
          'descripcion, link_grupo, publico) VALUES("'.
          $nombre.'","'.$descripcion.'","'.$link.'",'.$publico.')';
  }
  else
  {
    // construye la consulta de actualizacion de grupo colaborador 
    $consulta_grupo = 'UPDATE grupos_colaboradores SET nombre_grupo="'.
          $nombre.'", descripcion="'.$descripcion.'", link_grupo="'.$link.'" '.
          ', publico='.$publico.' WHERE id_grupo='.$registro['gc_id_grupo'];       
  }
  
  // realiza consulta de miembro
  $resultado = mysql_query($consulta_grupo, $conexion);

  // si da error, devuelve 0
  if (!$resultado)
  {
   echo "Error en la consulta ".$consulta_grupo;
   return 0;
  }

  // obtiene el valor del elemento insertado/actualizado
  if ($registro['gc_id_grupo'] == 0)
  {
     $id_grupo = mysql_insert_id();
  }  
  else
  {
     $id_grupo = $registro['gc_id_grupo'];
  }

  //---------------------------------------------
  // actualiza informacion de miembros existentes
  //---------------------------------------------
  for ($i=1; $i<=$registro['gc_num_mi']; $i++)
  {
      // verifica si hay que borrar miembro
      if ($registro["mi_borrar_$i"] == 1)
      {
          // construye consulta
          $consulta_borrar = 'DELETE FROM colaborador_proyectos '.
                             'WHERE id_colaborador='.$registro["mi_id_mie_$i"];
          // ejecuta consulta
          $resultado = mysql_query($consulta_borrar, $conexion);
          // si da error, muestralo
          if (!$resultado)
          {
            echo "Error en la consulta ".$consulta_borrar;
          }          
          // construye consulta
          $consulta_borrar = 'DELETE FROM colaboradores '.
                             'WHERE id_colaborador='.$registro["mi_id_mie_$i"];
          // ejecuta consulta
          $resultado = mysql_query($consulta_borrar, $conexion);
          // si da error, muestralo
          if (!$resultado)
          {
            echo "Error en la consulta ".$consulta_borrar;
          }             
      } 
      // sino hay que borrarlo, hay que actualizarlo
      else
      {
         // prepara datos para insertar desde web
         $mi_nombre = addslashes($registro["mi_nombre_$i"]);
         $mi_puesto = addslashes($registro["mi_puesto_$i"]);
         $mi_email  = addslashes($registro["mi_email_$i"]);
         $mi_link   = addslashes($registro["mi_link_$i"]);
         $mi_dir    = ($registro["mi_dir_$i"] == 1)? 1:0;
         
         // construye consulta de actualizacion
         $consulta_act = 'UPDATE colaboradores SET nombre="'.$mi_nombre.
           '", puesto="'.$mi_puesto.'", email_colaborador="'.$mi_email.
           '", link_colaborador="'.$mi_link.'", director='.$mi_dir.
           ' WHERE id_colaborador='.$registro["mi_id_mie_$i"];
           
          // ejecuta consulta
          $resultado = mysql_query($consulta_act, $conexion);
          // si da error, muestralo
          if (!$resultado)
          {
            echo "Error en la consulta ".$consulta_act;
          }  
      }
  }
  
  //-----------------------------------------------
  // Inserta nuevo miembro en el grupo
  //-----------------------------------------------
  if (strlen($registro["mi_nuevo"])>0)
  {
     // prepara datos
     $mi_nombre = addslashes($registro["mi_nuevo"]);
   
     // construye consulta de insercion
     $consulta_ins = 'INSERT INTO colaboradores(grupo_pertenece, nombre, '.
      'puesto, email_colaborador, link_colaborador) VALUES('.$id_grupo.
      ',"'.$mi_nombre.'", "", "", "")';
      
     // ejecuta consulta
     $resultado = mysql_query($consulta_ins, $conexion);
     // si da error, muestralo
     if (!$resultado)
     {
        echo "Error en la consulta ".$consulta_ins;
     }       
  }
  
  return $id_grupo;  
}
