<?
// ADVERTENCIA!!!
// requiere el include de los siguientes paquetes de un padre
// - config.php
// - common_pub.php

$mes_a_numero = array ( "jan" => 1, "feb" => 2, "mar" => 3, "apr" => 4, 
                  "may" => 5, "jun" => 6, "jul" => 7, "aug" => 8, "sep" => 9, 
                  "oct" => 10, "nov" => 11, "dec" => 12);
//----------------------------------------------------------------
// Funcion : Quitar_Llaves
// Descripcion: 
//    Elimina las llaves que abarcan a una cadena de texto
// Parametros:
//    La cadena de texto con las llaves
// Devuelve:
//    La cadena formateada sin llaves
//----------------------------------------------------------------
function Quitar_Llaves($cadena)
{
   $cadena_tmp = str_replace("{","",$cadena);
   $cadena_tmp = str_replace("}","",$cadena_tmp);
   return $cadena_tmp;
}
//----------------------------------------------------------------
// Funcion : Calcula_fecha_publicacion
// Descripcion:
//   Esta función calcula a partir de los campos year y month
//   pasados como parametros la fecha de publicacion insertada en la base de
//   datos. 
//    Si el campo year no está fijado, se pone a 9999.
//    Si el campo month no está fijado se pone a 01
//    el campo dia se pone siempre a 01
// Parametros:
//   registro : Entrada : La matriz de campos del registro
// Devuelve:
//   una cadena formateada 'YYYY-MM-DD'
//----------------------------------------------------------------
function Calcula_fecha_publicacion ($registro)
{
    global $mes_a_numero;
    // variable de fecha de publicacion
    $anyo_publicacion = '9999';
    $mes_publicacion  = '01';
    
    // comprobamos año
    if (isset($registro["year"]))
    {
      // quitale llaves
      $anyo = Quitar_Llaves($registro["year"]);
      // lee año como entero
      list($anyo) = sscanf($anyo,"%d");
      // asignalo si todavia tiene valor
      if (strlen($anyo)>0) $anyo_publicacion = $anyo;
    }

    // comprobamos mes
    if (isset($registro["month"]))
    {
      // formatea mes
      $mes = strtolower($registro["month"]);
      $mes = Quitar_Llaves($mes);
      // verificamos si mes introducido es valido
      if (array_key_exists($mes,$mes_a_numero))
      {
       $mes_publicacion = $mes_a_numero[$mes];
      }
    }
 
    // concatena cadena
    $fecha_publicacion = "'".$anyo_publicacion."-".$mes_publicacion."-01'";

    return $fecha_publicacion;
}

//----------------------------------------------------------------
// Funcion : Borra_Crossref
// Descripcion:
//   Esta función inserta una referencia cruzada de una publicacion
// Parametros:
//   id_ref : Entrada : El id del que hace la referencia cruzada
//   conexion : Entrada : El manejador de BD
// Devuelve
//   0 - si todo fue bien
//   1 - si hubo algún error de consulta de BD
//----------------------------------------------------------------
function Borra_Crossref($id_ref, $conexion)
{
  $consulta_borrar = 'DELETE FROM ref_relacion '.
   'WHERE referencia_cruzada=1 AND id_ref='.$id_ref;
  
  $resultado = mysql_query($consulta_borrar, $conexion);
  if (!$resultado)
  {
     echo "No se ejecuto la consulta ".$consulta_borrar;
     return 0;
  }
}

//----------------------------------------------------------------
// Funcion : Inserta_Crossref
// Descripcion:
//   Esta función inserta una referencia cruzada de una publicacion
// Parametros:
//   id_ref_bibtex : Entrada : El indentificador bibtex al que se hace
//                             referencia
//   id_ref : Entrada : El id del que hace la referencia cruzada
//   conexion : Entrada : El manejador de BD
// Devuelve
//   0 - si todo fue bien
//   1 - si hubo algún error de consulta de BD
//   2 - si no encontró la referencia que se intenta cruzar
//----------------------------------------------------------------
function Inserta_Crossref($id_ref_bibtex, $id_ref, $conexion)
{
  // quitale llaves a identificador
  $id_bibtex = Quitar_Llaves($id_ref_bibtex);
   
  $consulta_identificacion = "SELECT id_ref, id_campos ".
   'FROM referencias LEFT JOIN ref_relacion '.
   'ON referencias.id_referencia = ref_relacion.id_ref '.
   'WHERE referencia_cruzada=0 AND id_ref_bibtex ="'.$id_bibtex.'"';
      
   // realiza consulta en Base de datos
   $resultado = mysql_query($consulta_identificacion, $conexion);

   if (! $resultado) return 1;
   
   // mira si existe la referencia e inserta una referencia cruzada
   if (mysql_num_rows($resultado) == 0) return 2;  

   $identificadores = mysql_fetch_row($resultado);
   
   $consulta_cross = 'INSERT INTO ref_relacion(id_ref,'.
     'id_campos, referencia_cruzada, id_ref_cruzada) VALUES('.
     $id_ref.','.$identificadores[1].',1,'.$identificadores[0].')';
     
   $resultado = mysql_query($consulta_cross, $conexion);   
   
   if (! $resultado) return 1;
   
   return 0;
}

//----------------------------------------------------------------
// Funcion : Determina_Idioma_Publicacion
// Descripcion: 
//   Esta funcion determina el idioma de la publicacion en
//   funcion de la terminacion del descriptor o el campo OPTidioma.
//   El segundo sobreescribe al primero
// Parametros:
//   identificador : Entrada: El nombre del identificador
//   optidioma     : Entrada: Campo OPTidioma
//----------------------------------------------------------------
function Determina_Idioma_Publicacion($identificador, $optidioma)
{
   $terminacion = substr($identificador, strlen($identificador)- 3);
   
   if     ($terminacion == '_en') $idioma = 'ENG';
   elseif ($terminacion == '_sp') $idioma = 'SPA';
   else                           $idioma = 'NDF';
   
   if (strlen($optidioma) == 3)
   {
      $idioma = $optidioma;
   }
   
   return $idioma;
}

//----------------------------------------------------------------
// Funcion : Determina_Tipo_Enlace
// Descripcion:
//   Esta función determina de que tipo de enlace se trata
// Parametros:
//   link_referencia_pub : Entrada : La URL del enlace
// Devuelve
//   N - si no es un enlace valido
//   E - si se trata de una URL absoluta (enlace externo)
//   I - si es una URL relativa (enlace interno)
//----------------------------------------------------------------
function Determina_Tipo_Enlace ($link_referencia_pub)
{
  if (strlen($link_referencia_pub)>0)
  {                   
    // si es un path absoluto, entonces el link es externo
    if (strstr($link_referencia_pub,"://"))
    {
            $tipo_link_pub = 'E';
    }
    else // si no, es un link interno
    {
            $tipo_link_pub = 'I';
    }
  }
  else // no hay link
  {
         $tipo_link_pub = 'N';
  }
  
  return $tipo_link_pub;   
}


//----------------------------------------------------------------
// Funcion : Existe_Sinonimo_en_BD
// Descripcion:
//   Esta función determina si hay un sinonimo ya declarador en
//   la base de datos
// Parametros:
//   conexion : Entrada : El descriptor de la base de datos
//   sinonimo : Entrada : El sinonimo a buscar
// Devuelve
//   FALSE - si no esta declarado en la base de datos
//   TRUE - si esta declarado en la base de datos
//----------------------------------------------------------------
function Existe_Sinonimo_en_BD($conexion, $sinonimo)
{
   $consulta = 'SELECT id_cadena FROM ref_cadenas WHERE cadena="'.$sinonimo.'"';

   $resultado = mysql_query($consulta, $conexion);
   
   // mira si hay algun resultado
   $num_aciertos = mysql_num_rows($resultado);
   
   if ($num_aciertos > 0) return TRUE;
   else return FALSE;
}

//----------------------------------------------------------------
// Funcion : inserta_BD_referencias
// Descripcion:
//   Esta función inserta en la base de datos los registros de referencias
//   pasados como parametros.
// Parametros:
//   datosref : Entrada : La matriz de referencias a insertar
//   acciones: Salida: un log de inserciones. Cada elemento del array se
//             compone de un registro (CODIGO, MENSAJE) donde el código
//             puede ser uno de los siguientes:
//             0 - Error
//             1 - Actualizacion
//             2 - Insercion
//----------------------------------------------------------------
function inserta_BD_referencias($datosref, &$acciones)
{

  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;
  // incluido desde common_pub;
  global $public_estado_defecto;
  global $public_rel_tipos;
  global $public_tipos_bibtex;
  
  // asigna los registros y los sinonimos
  $registros = array();
  $registros = $datosref['bib'];
  $sinonimos = array();
  $sinonimos = $datosref['sin'];
  
  // lista de corssref
  $lista_crossref = array();
  
  // contador de avisos
  $contador_acciones = 1;
  
  // conecta a Base de Datos MySQL
  $conexion = mysql_connect("localhost",$USER_BD,$PASS_BD);
  // verifica si se abrió conexion
  if (!$conexion)
  {
     echo "Error al conectarse a la base de datos MYSQL\n";
     return;
  }
  
  // selecciona base de datos
  mysql_select_db($BASE_DATOS,$conexion);
  
  // -----------------------------------------------
  // inserta todos los sinonimos en la base de datos
  // -----------------------------------------------
  for (reset($sinonimos); $sinonimo = key($sinonimos); next($sinonimos))
  {
    if (Existe_Sinonimo_en_BD($conexion, $sinonimo))
    {
     $consulta_sinonimo = 'UPDATE ref_cadenas SET valor="'.$sinonimos[$sinonimo]
      .'" WHERE cadena="'.$sinonimo.'"';
    }
    else
    {
     $consulta_sinonimo = 'INSERT INTO ref_cadenas(cadena, valor) VALUES("'.
        $sinonimo.'","'.$sinonimos[$sinonimo].'")';
    }
    // ejecuta consulta
    $resultado = mysql_query($consulta_sinonimo,$conexion);
    
    // si dio error, insertalo en la lista de acciones
    if (!$resultado)
    {
        $acciones[$contador_acciones] = array (0,"ERROR de sinonimos: ".
                  $sinonimo." .No se pudo realizar la consulta ".
                  $consulta_sinonimo);
        $contador_acciones++;
    }
   
  }
  
  
  // recorre todos los elementos del array por tipo e identificador
  for (reset($registros); $tipo_reg = key($registros); next($registros))
  {
   for (reset($registros[$tipo_reg]); 
        $id_ref_bibtex = key($registros[$tipo_reg]); 
        next($registros[$tipo_reg]))
   {
      // ------------------------------------------------------
      // Prepara los valores de la referencia
      // ------------------------------------------------------
      // calcula la fecha de publicacion
      $fecha_publicacion = Calcula_fecha_publicacion(
                                        $registros[$tipo_reg][$id_ref_bibtex]);
      // mira fecha de referencia cruzada si no tiene una dada
      if (($fecha_publicacion == "'9999-01-01'") &&
          (strlen($registros[$tipo_reg][$id_ref_bibtex]['crossref']) > 0))
      {
         $id_ref_crossref = Quitar_Llaves(
                            $registros[$tipo_reg][$id_ref_bibtex]['crossref']);
	 
         for (reset($public_tipos_bibtex); 
              $tipo_pub = current($public_tipos_bibtex);
              next($public_tipos_bibtex))
         {
           if (array_key_exists($tipo_pub, $registros))
           {
             if (array_key_exists($id_ref_crossref, $registros[$tipo_pub]))
             {
               $fecha_publicacion = Calcula_fecha_publicacion(
                                       $registros[$tipo_pub][$id_ref_crossref]);
             }
           }
         }
      }                                  
                                        
      // determina idioma
      $idioma_pub = Determina_Idioma_Publicacion ($id_ref_bibtex, 
        $registros[$tipo_reg][$id_ref_bibtex]['OPTidioma']);

      // determina enlace si está presente
      $link_referencia_pub = Quitar_Llaves(
                           $registros[$tipo_reg][$id_ref_bibtex]['OPTenlace']);
      $tipo_link_pub = Determina_Tipo_Enlace($link_referencia_pub);
      
      // obtiene estado y visibilidad de publicacion
      if (strlen($registros[$tipo_reg][$id_ref_bibtex]['OPTestado'])>0)
      {
        // convierte a uppercase y quitale llaves
        $estado_pub = Quitar_Llaves(
                            $registros[$tipo_reg][$id_ref_bibtex]['OPTestado']);
        $estado_pub = strtoupper($estado_pub);
      }
      else
      { // si no está definido OPTestado, sobreescribelo como el valor
        // de estado por defecto
        $estado_pub = $public_estado_defecto;
      }

      $visibilidad = AUX_estado_es_visible($estado_pub);
      
      //  Da un aviso y no inserta si no es un estado valido
      if ($visibilidad == -1)
      {
         $acciones[$contador_acciones] = array (0,"ERROR de actualización: ".
               $id_ref_bibtex." tiene un estado inválido");
         $contador_acciones++;
         continue;         
      }

      // verifica el tipo de publicacion
      if (strlen($registros[$tipo_reg][$id_ref_bibtex]['OPTtipopub'])>0)
      {
         // verifica que es un tipo valido
         $tipo_formateado = trim(strtoupper(
          $registros[$tipo_reg][$id_ref_bibtex]['OPTtipopub']));
         
         for (reset($public_rel_tipos); $tipo_web = key($public_rel_tipos);
          next($public_rel_tipos))
         {
            if (in_array($tipo_formateado,$public_rel_tipos[$tipo_web]))
            {
               $tipo_busqueda = $tipo_formateado;
            }
         }
      }
      else
      {
         $tipo_busqueda = $tipo_reg;
      }

      // ------------------------------------------------------------
      // Ejecuta la consulta de Insercion/Actualizacion de referencia
      // ------------------------------------------------------------      
      // chequea si ya hay un registro con el mismo id_ref_bibtex
      $consulta_identificacion = "SELECT id_ref, id_campos ".
        'FROM referencias LEFT JOIN ref_relacion '.
        'ON referencias.id_referencia = ref_relacion.id_ref '.
        'WHERE referencia_cruzada=0 AND id_ref_bibtex ="'.$id_ref_bibtex.'"';
      
      // realiza consulta en Base de datos
      $resultado = mysql_query($consulta_identificacion, $conexion);      
      
      // si hay identificador, es una actualización
      if ($identificador_tmp = mysql_fetch_row($resultado))
      {
         $id_ref   = $identificador_tmp[0];
         $id_campo = $identificador_tmp[1];
         
         // borra todas las referencias cruzadas
         $consultas_borrar[1] = "DELETE FROM ref_relacion WHERE ".
          "referencia_cruzada=1 AND id_ref=".$id_ref;         
         // Borra todos los campos del registro
         $consultas_borrar[2] = "DELETE FROM ref_campos WHERE id_campo_ref=".
           $id_campo;
           
         // ejecuta consultas
         for ($i=1; $i<3; $i++)
         {
            $resultado = mysql_query($consultas_borrar[$i], $conexion);
            if (!$resultado)
            {
               $acciones[$contador_acciones] = array (0,"ERROR de actualización: ".
                  $id_ref_bibtex." .No se pudo realizar la consulta ".
                  $consultas_borrar[$i]);
               $contador_acciones++;
            }
         } 
         
         // realiza la consulta de actualización
         $consulta_update = "UPDATE referencias SET tipo='".$tipo_busqueda.
           "', tipo_bibtex='".$tipo_reg."', fecha_publicacion=".
           $fecha_publicacion.", visible=".$visibilidad.', idioma="'.
           $idioma_pub.'", estado="'.$estado_pub.'",tipo_link="'.$tipo_link_pub.
           '", link_referencia="'.$link_referencia_pub.
           '" WHERE id_referencia='.$id_ref;
        
         // ejecuta consulta
         $resultado = mysql_query($consulta_update, $conexion); 
         
         // actualiza el log de acciones
         if (!$resultado)
         {
            $acciones[$contador_acciones] = array (0,"ERROR de actualización: ".
               $id_ref_bibtex." .No se pudo realizar la consulta ".
               $consulta_update);
         } 
         else
         {
            $acciones[$contador_acciones] = array(1,
              "Actualización de <a href='public_editar.php?id_ref=".
              $id_ref."'>".$id_ref_bibtex.
              "</a> realizada correctamente");
         }
         $contador_acciones++;
      }
      else // si no, es una inserción
      {
   
         // inserta el registro en la TABLA referencias
         $consulta_insertar = 'INSERT INTO referencias(id_ref_bibtex, tipo,'.
           'tipo_bibtex, fecha_publicacion, visible, idioma, estado,'.
           'tipo_link, link_referencia) values("'.
           $id_ref_bibtex.'","'.$tipo_busqueda.'","'.$tipo_reg.'",'.
           $fecha_publicacion .','.$visibilidad.',"'.$idioma_pub.'","'.
           $estado_pub.'","'.$tipo_link_pub.'","'.$link_referencia_pub.'")';
   
         // inserta consulta en Base de datos
         $resultado = mysql_query($consulta_insertar, $conexion);

         // actualiza el log de acciones
         if (!$resultado)
         {
            $acciones[$contador_acciones] = array (0,"ERROR de inserción: ".
               $id_ref_bibtex." .No se pudo realizar la consulta ".
               $consulta_insertar);
            $id_ref = 0;
         } 
         else
         {
            //obtiene el valor de ID de la insercion
            $id_ref = mysql_insert_id();
              
            $acciones[$contador_acciones] = array(2,
            "Inserción de <a href='public_editar.php?id_ref=".$id_ref.
            "'>".$id_ref_bibtex."</a> realizada correctamente");
         }
         $contador_acciones++;
      
         if ($id_ref != 0)
         {
           // obtiene un identificador valido para los campos e inserta
           // la referencia general con ese identificador
           $consulta_id = 'SELECT MAX(id_campos) FROM ref_relacion';
           $resultado =  mysql_query($consulta_id, $conexion);
           $identificador_tmp = mysql_fetch_row($resultado);
           $id_campo = $identificador_tmp[0] + 1;
           
           $consulta_cross = 'INSERT INTO ref_relacion(id_ref,'.
            'id_campos, referencia_cruzada, id_ref_cruzada) VALUES('.
            $id_ref.','.$id_campo.',0,0)';
            
           $resultado =  mysql_query($consulta_cross, $conexion);
           
           if (! $resultado)
           {
            $acciones[$contador_acciones] = array (0,"ERROR de inserción: ".
               $id_ref_bibtex." .No se pudo realizar la consulta ".
               $consulta_cross);
            $contador_acciones++;             
           }
         }
      }

      // ------------------------------------------------------------
      // Ejecuta la consulta de Insercion de Campos
      // ------------------------------------------------------------ 
      // verifica si se inserto correctamente
      if ($acciones[$contador_acciones - 1][0] != 0)
      {
        
         // recorre todos los campos e insertalos en
         // la tabla REF_CAMPOS salvo el campo OPTidioma y OPTestado
         for (reset($registros[$tipo_reg][$id_ref_bibtex]); 
                    $campo = key($registros[$tipo_reg][$id_ref_bibtex]); 
                    next($registros[$tipo_reg][$id_ref_bibtex]))
         {
            if (($campo != 'OPTidioma')&&
                ($campo != 'OPTestado')&&
                ($campo != 'OPTenlace'))
            {
               // obtiene valor de array
               $valor = $registros[$tipo_reg][$id_ref_bibtex][$campo];
               $valor = addslashes($valor);
               
               // prepara la consulta de insercion
               $consulta_insertar = "INSERT INTO ref_campos(id_campo_ref,".
                "campo, valor) values(".$id_campo.",'".$campo."','".$valor.
                "') ";
               
               // inserta consulta en BD
               $resultado = mysql_query($consulta_insertar, $conexion); 
               
               // verifica resultado
               if (!$resultado)
               {
                  $acciones[$contador_acciones] =array (0, 
                     "ERROR de inserción de campo: ".
                     $id_ref_bibtex." .No se pudo realizar la consulta ".
                     $consulta_insertar);
                  $contador_acciones++;
               }
               
               // si el campo es un crossref, insertamos referencia cruzada
               if ($campo == 'crossref')
               {
	         $lista_crossref[$valor]=$id_ref;
               }
               
            }  
         }
      }
      
      // Da un aviso si el registro es visible y no tiene fecha
      if (($visibilidad == 1)&&($fecha_publicacion == "'9999-01-01'"))
      {
         $acciones[$contador_acciones] = array (3,"AVISO de actualización: ".
               $id_ref_bibtex." esta publicado sin fecha");
         $contador_acciones++;         
      }            
   }
  }
  
  // inserta todas las crossref
  for (reset($lista_crossref);
       $valor = key($lista_crossref);
       next($lista_crossref))
  {
     $id_ref = $lista_crossref[$valor];
     $resultado = Inserta_Crossref($valor, $id_ref, $conexion);
                
     if ($resultado == 1)
     {
                  $acciones[$contador_acciones] =array (0, 
                     "ERROR realizando referencia cruzada: ".
                     $id_ref_bibtex." .No se pudo realizar la consulta");
                  $contador_acciones++;
     }
     else if ($resultado == 2)
     {
                  $acciones[$contador_acciones] =array (0, 
                     "ERROR realizando referencia cruzada: ".
                     $id_ref_bibtex." .No se encontro destino -> ".$valor);
                  $contador_acciones++;                  
     }
  }  
  // cierra conexion MySQL
  mysql_close($conexion);

}



?>
