<?php
// Inicializamos el archivo con el script
include('common/init.php');

include('common/common_pub.php');

$id_miembro = $_SESSION['id_usuario'];
$public_marcacion_campos = cargar_formatos($public_marcacion_campos, $id_miembro, $conexion);

//--------------------------------------------------------------------------
// public_busqueda.php
//
// Genera la página de resultados de busqueda de las publucaciones
// y los exporta al template publicaciones.html
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

  // definicion de Commons usados
  global $public_rel_tipos;
  global $public_traduc_campos;
  global $public_num_campos;
  global $public_por_pagina;
  global $public_tipo_links;
  global $public_estados_visibles;




  // definiciones dependientes del idioma utilizadas
  // $public_tipos_refer;
  // $public_logica_busqueda
  // $gen_separador_campos

  // definicion de variables usadas en todo el script
  $cadena_busqueda = "";
  $numero_pagina = (strlen($_GET['pagina'])>0)?  $_GET['pagina'] : 1;

  // crea parser de la página
  $pagina=new XTemplate ("templates/es/public_exportar_l.html");

  // conecta a Base de Datos MySQL
  $conexion = mysql_connect("localhost",$USER_BD,$PASS_BD);
  // verifica si se abrió conexion
  if (!$conexion)
  {
     ERR_muestra_pagina_error($gen_error_conexion, "");
     return;
  }

  // selecciona base de datos
  mysql_select_db($BASE_DATOS,$conexion);
 //--------------------------------------------------------------------
 // LEE LOS SINONIMOS DE LA TABLA
 //--------------------------------------------------------------------
 $lista_sinonimos = array();
 $lista_cond_sin  = array();

 $consulta_sinonimos = 'SELECT cadena, valor FROM ref_cadenas';
 $resultado = mysql_query($consulta_sinonimos, $conexion);

 while ($fila = mysql_fetch_row($resultado))
 {
   $lista_sinonimos["$fila[0]"] = $fila[1];
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
 //--------------------------------------------------------------------
 // CONSULTA DE TIPOS DE PUBLICACION
 //--------------------------------------------------------------------
 // definicion de consultas de la base de datos
 $cons_tipos_referencias ='SELECT DISTINCT(tipo) FROM referencias ';

 // inicializa array de tipos presentes
 $tipos_presentes = array();

 // realiza consulta
 $resultado = mysql_query($cons_tipos_referencias, $conexion);

 // si la consulta fue bien
 if ($resultado)
 {
   // recorre resultados y registra tipos
   while ($tipo_obtenido = mysql_fetch_row($resultado))
   {
     $tipo_web = AUX_convertir_tipos($tipo_obtenido[0]);
     $tipos_presentes[$tipo_web] = 1;
   }
 }
 else
 {
   echo "Error al realizar la consulta : ".$cons_tipos_referencias."\n";
 }

 // bucle de publicacion de cada tipo presente.
 for (reset($public_rel_tipos);
       $tipo_consultado = key($public_rel_tipos);
       next($public_rel_tipos))
  {
     if ($tipos_presentes[$tipo_consultado] == 1)
     {
         // si hay más de uno, inserta elemento en la tabla
         $valores_lista['VAL1']=$tipo_consultado;
         $valores_lista['VAL2']="[".$public_tipos_refer[$tipo_consultado]."]";
         // metelos en el navegador de la página
         $pagina->assign("LISTA1",$valores_lista);
         $pagina->parse("main.tabla_direc.fila");
     }
  }
  // limpia array de insercion en página
  unset($valores_lista);

  // termina tabla
  $pagina->parse("main.tabla_direc");

//--------------------------------------------------------------------
// LISTA DE PARAMETROS DE BUSQUEDA
//--------------------------------------------------------------------

  // inicializa variable destino
  $lista_parametros = array();
  $logica = "";

  // inserta lógica en cabecera
  if (strlen($_GET['logica'])>0)
  {
    $pagina->assign("LOGICA",$public_logica_busqueda[$_GET['logica']]);
    $cadena_busqueda .= "logica=".$_GET['logica']."&";
  }
  else
  {
    $pagina->assign("LOGICA",$public_logica_busqueda['AND']);
    $cadena_busqueda .= "logica=AND&";
  }

  // inserta en página
  $pagina->parse("main.tabla_parametros.logica_busqueda");

  // para cada uno de los parámetros de busqueda validos.
  for ($i=1; $i< $public_num_campos+1; $i++)
  {
   if (strlen($_GET["campo$i"])>0)
   {

     $lista_parametros['CAMPO'] = htmlentities($_GET["campo$i"]);
     $lista_parametros['VALOR'] = $_GET["valor$i"];
     // insertalos en página
     $pagina->assign("LISTA_PARAMETROS",$lista_parametros);
     $pagina->parse("main.tabla_parametros.lista_parametros");
     // reconstruye cadena de busqueda
     $cadena_busqueda .= "campo$i=".$_GET["campo$i"]."&valor$i=".$_GET["valor$i"]."&";
   }
  }
  // añade campo auxiliar si está definido
  if (strlen($_GET['campo_aux'])>0)
  {
     // recolecta valores
     $lista_parametros['CAMPO'] = $_GET['campo_aux'];
     $lista_parametros['VALOR'] = $_GET['valor_aux'];
     // insertalo en página
     $pagina->assign("LISTA_PARAMETROS",$lista_parametros);
     $pagina->parse("main.tabla_parametros.lista_parametros");
     // reconstruye cadena de busqueda
     $cadena_busqueda .= "campo_aux=".$_GET["campo_aux"]."&valor_aux=".$_GET["valor_aux"]."&";
  }
  // añade tipo si está definido
  if (strlen($_GET['tipo'])>0)
  {
     // insertalo en página
     $pagina->assign("VALOR",$public_tipos_refer[$_GET['tipo']]);
     $pagina->parse("main.tabla_parametros.tipo_publicacion");
     // reconstruye cadena de busqueda
     $cadena_busqueda .= "tipo=".$_GET['tipo']."&";
  }
  // añade identificador si está definido
  if (strlen($_GET['id_ref_bibtex']))
  {
     // insertalo en página
     $pagina->assign("VALOR",$_GET['id_ref_bibtex']);
     $pagina->parse("main.tabla_parametros.id_bibtex");
     // reconstruye cadena de busqueda
     $cadena_busqueda .= "id_ref_bibtex=".$_GET['id_ref_bibtex']."&";
  }
  // añade estado si está definido
  $estados_definidos = "";
  $lista_estados = array();
  for (reset($public_estados_visibles);
       $estado_pub = key($public_estados_visibles);
       next($public_estados_visibles))
  {
    if ($_GET["E_$estado_pub"] == 1)
    {
       $estados_definidos .= ' "'.$estado_pub. '"';
       array_push($lista_estados, $estado_pub);
       // reconstruye cadena de busqueda
       $cadena_busqueda .= "E_".$estado_pub."=1&";
    }
  }
  if (strlen($estados_definidos) > 0)
  {
       // insertalo en página
       $pagina->assign("VALOR",$estados_definidos);
       $pagina->parse("main.tabla_parametros.estado_pub");
  }
  // insertalo en página
  $pagina->assign("VALOR",$numero_pagina);
  $pagina->parse("main.tabla_parametros.pagina");
  // finaliza tabla
  $pagina->parse("main.tabla_parametros");

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

  // verifica si existe campo auxiliar
   if (isset($_GET["campo_aux"]) && (strlen($_GET["campo_aux"])>0) )
    {$campo_auxiliar = $_GET["campo_aux"];}

  // calcula los registros a mostrar segun el número de página
  //$pag_lim_inferior=(($numero_pagina-1)*$public_por_pagina);
  //$pag_lim_superior=$numero_pagina*$public_por_pagina;
  $pag_lim_inferior = 0;
  $pag_lim_superior = 1500;

  // Para cada una de los tipos, haz la consulta de publicaciones
  // de esta forma conseguimos la ordenación deseada en los tipos
  for (reset($lista_bucle_tipos);
       $tipo_consultado = current($lista_bucle_tipos);
       next($lista_bucle_tipos))
  {
   $anyo_publicacion = "";
   // construye consulta de obtencion de publicaciones según los parámetros
   // de busqueda
   $consulta_id = "SELECT DISTINCT(id_referencia), COUNT(id_campo_ref), tipo, ".
     "tipo_link, link_referencia, YEAR(fecha_publicacion), id_campo_ref ".
     "FROM referencias LEFT JOIN ref_relacion ".
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
       // incrementa el número de registros insertados
       $registros_encontrados ++;

       // muestra solo aquellos que esten en la página
       if (($pag_lim_inferior < $registros_encontrados) AND
          ($registros_encontrados <= $pag_lim_superior))
       {
          // inicializa array de publicaciones
          $campos_public = array();
          $tipo_web_actual = AUX_convertir_tipos($id_referencia[2]);
          // verifica si tiene que insertar cabecera de tipo
          if ($tipo_cabecera != $tipo_web_actual)
          {
          	 // termina tabla anterior si no es la primera
          	 if (strlen($tipo_cabecera)>0)
          	 {
          	   $pagina->parse("main.tipo_grupo.lista");
          	   
          	 }
            // asigna tipo
          	 $tipo_cabecera = $tipo_web_actual;
            // mete tipo en la página
            $pagina->assign("TIPO_PUBLICACION",
                                           $public_tipos_refer[$tipo_cabecera]);
            $pagina->parse("main.tipo_grupo.cabecera_tipo");
          }
          // construye segunda consulta para obtener campos de la publicacion
          $consulta_campos = "SELECT campo,valor FROM ref_campos WHERE ".
                            "id_campo_ref=".$id_referencia[6];

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
               $campos_public["$campos_fila[0]"] = $campos_fila[1];
               AUX_sustitucion_sinonimos ($lista_sinonimos,
                                      $campos_public["$campos_fila[0]"]);
            }

            // si tiene un campo crossref, obtiene sus campos
            if (strlen($campos_public['crossref'])>0)
            {
               // construye consulta
               $consulta_crossref = 'SELECT campo,valor '.
                'FROM ref_relacion LEFT JOIN ref_campos '.
                'ON ref_relacion.id_campos=ref_campos.id_campo_ref '.
                'WHERE referencia_cruzada=1 AND id_ref='.$id_referencia[0];

               // ejecutala y obtiene campos nuevos
               $resul_campos = mysql_query($consulta_crossref, $conexion);
               if ($resul_campos)
               {
                  while($campos_fila = mysql_fetch_row($resul_campos))
                  {
                     // asigna solo si no hay ya un campo del hijo
                     if (strlen($campos_public["$campos_fila[0]"])== 0)
                     {
                      $campos_public["$campos_fila[0]"] = $campos_fila[1];
                      AUX_sustitucion_sinonimos ($lista_sinonimos,
                                      $campos_public["$campos_fila[0]"]);
                     }
                  }
               }
            }

            // si el año a cambiado respecto al anterior,
            // inserta nueva cabecera de año
            if ($id_referencia[5] != $anyo_publicacion)
            {
               // cierra la lista anterior
               $pagina->parse("main.tipo_grupo.lista");
               // asigna nuevo año
               $anyo_publicacion = $id_referencia[5];
               $anyo_public_str = str_replace("9999","---",$anyo_publicacion);
               // inserta cabecera de año y cierra lista
               $pagina->assign("ANYO_PUBLICACION",$anyo_public_str);
               $pagina->parse("main.tipo_grupo.lista.cabecera_anyo");
            }

            // inserta la cadena formateada en la pagina
            $pagina->assign("REFERENCIA",
               AUX_campos_formateados($campos_public,"",$gen_separador_campos));
            $pagina->assign("ID_REFERENCIA",$id_referencia[0]);

            if ($id_referencia[3] == $public_tipo_links['Interno'])
	    {
	     $nombre_fichero = substr($id_referencia[4],
	       strrpos($id_referencia[4],"/"));
	     $pagina->assign("LINK_PUB",'docs/'.$id_referencia[4]);
	    }
	    else
	    {
             $pagina->assign("LINK_PUB",$id_referencia[4]);
            }
            // asigna Link a publicacion si lo tiene
            if ($id_referencia[3] != $public_tipo_links['No Disponible'])
            {
                $extension = strtoupper(
                  substr($id_referencia[4],strrpos($id_referencia[4],".")+1));
                $pagina->assign("EXT","[".$extension."]");
            }
            else $pagina->assign("EXT","");

            // escribe resultados en página
            $pagina->parse("main.tipo_grupo.lista.fila");

            // libera resultados de la consulta
            mysql_free_result ($resul_campos);
          }
       } // If de chequeo de consulta
    } // Cierra el bucle de consulta de cada registro
   } // If de chequeo de consulta
  } // Cierra el bucle FOR de cada tipo consultado

   // cierra la lista de publicaciones
   $pagina->parse("main.tipo_grupo.lista");
   $pagina->parse("main.tipo_grupo");


  mysql_close($conexion);

  // imprime pagina completa
  $pagina->parse("main");
  $texto = $pagina->text("main");

  if(isset($_GET['rtf'])) {
        $texto = str_replace("<html>", "{\\rtf1\\ansi{\\fonttbl\\f0\\fswiss Helvetica;}\\f0\\pard
", $texto);
        $texto = str_replace("</html>", "}", $texto);

        $texto = str_replace("<body>", "", $texto);
        $texto = str_replace("</body>", "", $texto);

        $texto = str_replace("<h2>", "{\\fs48\\b ", $texto);
        $texto = str_replace("</h2>", "}\\par\\par ", $texto);

        $texto = str_replace("<h3>", "{\\fs36\\b ", $texto);
        $texto = str_replace("</h3>", "}\\par ", $texto);

        $texto = str_replace("<strong>", "{\\b ", $texto);
        $texto = str_replace("</strong>", "} ", $texto);

        $texto = str_replace("<em>", "{\\i ", $texto);
        $texto = str_replace("</em>", "} ", $texto);

        $texto = str_replace("<p>", "", $texto);
        $texto = str_replace("</p>", "\\par\\par ", $texto);

        $texto = str_replace("  ", "", $texto);
        $texto = str_replace("\n", "", $texto);

        /* En los encabezados indicamos que se trata de un documento de MS-WORD
        y en el nombre de archivo le ponemos la extensión RTF.            */
        header('Content-type: application/msword');
        header('Content-Disposition: inline; filename=prueba1.rtf');
        /*  Enviamos el documento completo a la salida  */
        echo $texto;

  } else {
      echo $texto;
  }

?>