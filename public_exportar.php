<?php
// Inicializamos el archivo con el script
include('common/init.php');

include('common/common_pub.php');

// fija el contenido como texto plano
header("Content-Type: text/plain"); 
  

include "bibtex/funciones_bibtex.php";
//--------------------------------------------------------------------------
// public_exportar.php
//
// Genera una página de resultados de busqueda de las publicaciones
// en formato bibtex.
// Los parametros de entrada de busqueda son:
//
//  - tipo : El tipo de publicacion
//  - logica : Coincide algún campo o Todos los campos
//  - campo[1..3] : El tipo de campo a buscar
//  - valor[1..3] : El tipo de valor a buscar
//
// Los valores impresos en página son:
//  - Lista de parametros de busqueda
//  - Lista de referencias encontradas
//--------------------------------------------------------------------------
  // definicion de Config usados
  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;
  
  // definicion de Commons usados
  global $public_rel_tipos;
  global $public_traduc_campos;
  global $public_num_campos;
  global $public_por_pagina;
  global $public_estados_visibles;
  
  // definicion de variables usadas en todo el script
  $cadena_busqueda = "";
  // imprime cabecera de texto plano

  
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

 //--------------------------------------------------------------------
 // LEE LOS SINONIMOS DE LA TABLA
 //-------------------------------------------------------------------- 

 $lista_cond_sin  = array();
 
 $consulta_sinonimos = 'SELECT cadena, valor FROM ref_cadenas';
 $resultado = mysql_query($consulta_sinonimos, $conexion);
 
 while ($fila = mysql_fetch_row($resultado))
 {
   echo "\n@string{".$fila[0]." = ".BIBTex_restitucion_acentos($fila[1])."}";
   // buscamos occurrencias dentro de tabla
   for ($i=1; $i< $public_num_campos+1; $i++)
   {
     if (strlen($_GET["campo$i"])>0)
     {
      if (strpos($fila[1],$_GET["valor$i"]))
      {  // construye condicion de sinonimos
         $lista_cond_sin[$i] .= "OR valor LIKE '%".$fila[0]."%' ";
      }
     }
   }
 }
 mysql_free_result($resultado);
 echo "\n\n";

//------------------------------------------------------------------
// LISTA PUBLICACIONES ENCONTRADAS
//------------------------------------------------------------------ 

  // Calcula numero mínimo de ocurrencias necesarias para
  // mostrar registro
  $numero_ocurrencias = AUX_calcula_num_ocurrencias();

  // inicializa tipo de cabecera
  $tipo_cabecera = "";
  $registros_encontrados = 0;
  
  // verifica si se va a realizar una busqueda por cada tipo o solo un tipo
  if (strlen($_GET['tipo'])>0)
    {$lista_bucle_tipos = array($_GET['tipo']);}
  else // si no hay definicion de tipo, mete en array todos los tipos
    {$lista_bucle_tipos = array_keys($public_rel_tipos);}

  // añade estados si está definido
  $lista_estados = array();
  for (reset($public_estados_visibles);
       $estado_pub = key($public_estados_visibles);
       next($public_estados_visibles))
  {
    if ($_GET["E_$estado_pub"] == 1)
    {
       array_push($lista_estados, $estado_pub);
    }
  }

  // Para cada una de los tipos, haz la consulta de publicaciones
  // de esta forma conseguimos la ordenación deseada en los tipos
  for (reset($lista_bucle_tipos);
       $tipo_consultado = current($lista_bucle_tipos);
       next($lista_bucle_tipos))
  {

   // construye consulta de obtencion de publicaciones según los parámetros
   // de busqueda
   $consulta_id = "SELECT DISTINCT(id_referencia), COUNT(id_campo_ref), ".
        "tipo_bibtex, id_ref_bibtex, idioma, estado, link_referencia, ".
        "tipo_link FROM referencias LEFT JOIN ref_relacion ".
        "ON referencias.id_referencia=ref_relacion.id_ref ".
        "LEFT JOIN ref_campos ".
        "ON ref_relacion.id_campos=ref_campos.id_campo_ref ";
   $condicion_busqueda = "WHERE ".
                        AUX_condicion_tipos($tipo_consultado).
                        AUX_condicion_bibtex($_GET['id_ref_bibtex']).
                        AUX_condicion_estado($lista_estados).
                        AUX_condicion_campos($lista_cond_sin);
   $agrupacion = "GROUP BY id_referencia ";
   $ordenacion = "ORDER BY fecha_publicacion DESC";

  
   // Construye consulta completa
   $consulta_id .= $condicion_busqueda.$agrupacion.$ordenacion;
  
   // realiza consulta de seleccion de id's
   $resultado = mysql_query($consulta_id, $conexion);  
  
   if (! $resultado)
   {
    echo "Error al realizar la consulta : ".$consulta_id."\n";      
   }
   else
   {
    // para cada id obtenido 
    while ($id_referencia = mysql_fetch_row($resultado))
    {   
      
       // no imprimas valor si el numero de ocurrencias
       // es menor del esperado y la lógica es AND
       if (($id_referencia[1]< $numero_ocurrencias) AND 
           ($_GET['logica']=='AND'))
       {
         continue;
       }  

       // escribe por pantalla su tipo y identidad
       echo "\n@".$id_referencia[2]."{".$id_referencia[3];

       // construye segunda consulta para obtener campos de la publicacion
       $consulta_campos = "SELECT campo,valor FROM ref_relacion ".
         'LEFT JOIN ref_campos '.
         'ON ref_relacion.id_campos=ref_campos.id_campo_ref '.
         "WHERE referencia_cruzada=0 AND id_ref=".$id_referencia[0].
         ' ORDER BY id_campo';
                         
       // obtiene campos de la publicacion seleccionada
       $resul_campos = mysql_query($consulta_campos, $conexion); 
       
       if (! $resul_campos)
       {
         echo "Error al realizar la consulta : ".$consulta_campos."\n";
       }
       else
       {
         // obtiene campos y los inserta en el array asociativo
         while ($campos_fila = mysql_fetch_row($resul_campos))
         {
            echo ",\n  ".$campos_fila[0]." = ".
            BIBTex_restitucion_acentos($campos_fila[1]);
         }        
   
         // libera resultados de la consulta
         mysql_free_result ($resul_campos);
       }
       // imprime el campo OPTidioma, OPTestado y OPTenlace
       echo ",\n  OPTidioma = ".$id_referencia[4];
       echo ",\n  OPTestado = ".$id_referencia[5];
       if ($id_referencia[7] != 'N')
       {
        echo ",\n  OPTenlace = ".$id_referencia[6];
       }
       // imprime final de registro
       echo "\n}\n";
 
    } // Cierra el bucle de consulta de cada registro 
   } // If de chequeo de consulta  
  } // Cierra el bucle FOR de cada tipo consultado

  // cierra descriptor
  mysql_close($conexion);

?>