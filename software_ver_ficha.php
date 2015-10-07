<?php
// Inicializamos el archivo con el script
include("common/init.php");
include("autenticacion.php");
// Autenticamos al usuario
autenticar_usuario();

$pagina = $contenido;
//--------------------------------------------------------------------------
// software_ver_ficha.php
//
// Genera la ficha de un software en cuestion. Para ello necesita que
// se le pase como parámetro la identidad de software. Si la identidad
// no está disponible, se muestra la página de error.
//
// Parametros de entrada
//   ids : Identidad del software
//--------------------------------------------------------------------------

  //--------------------------------------------------------------------
  // CONSULTA DE DATOS DE SOFTWARE
  //--------------------------------------------------------------------   
  // definicion de consultas de la base de datos
  $consulta_proyecto ='SELECT titulo, descripcion, id_sw_bibtex,'.
    ' sistema_operativo, licencia, link_licencia, email_soporte, link_homepage'.
    ' FROM software LEFT JOIN software_idiomas '.
    ' ON software.id_software = software_idiomas.id_software '.
    ' WHERE idioma="'.$idioma.'" AND software.id_software='.$_GET['ids'];

  // realiza consulta de miembro
  $resultado = mysql_query($consulta_proyecto, $conexion);  

  // obtiene datos del miembro
  $software = mysql_fetch_row($resultado);  


  // construye cadena de busqueda de software
  $documentos = 'campo1=OPTprograma&valor1='.$software[2];

  $lista_valores = array(
         'TITULO' => $software[0],
         'DESCRIPCION' => $software[1],
         'OP_SYSTEM' => $software[3],
         'LICENCIA' => $software[4],
         'LINK_LICENCIA' => $software[5],
         'EMAIL' => $software[6],
         'DOCUMENTOS' => $documentos,
         'LINK_HOMEPAGE' => $software[7]); 
  
  // imprime los valores en página
  $pagina->assign("LISTA",$lista_valores);
  $pagina->parse("main.tabla_ficha.datos_software");

  //--------------------------------------------------------------------
  // CONSULTA DE PAQUETES DE SOFTWARE
  //--------------------------------------------------------------------   
  // verifica si hay ordenacion
  if ((strlen($_GET['ordtype'])>0) && (strlen($_GET['order'])>0))
  {
    if ($_GET['order'] == 'up')    $orden_busqueda = 'ASC';
    else  $orden_busqueda = 'DESC';
    
    if ($_GET['ordtype'] == 'nombre') $tipo_orden = 'NOMBRE';
    else if ($_GET['ordtype'] == 'version') $tipo_orden = 'VERSION';
    else $tipo_orden = 'FECHA';
    
    $ordenacion = ' ORDER BY '.$tipo_orden.' '.$orden_busqueda;
  }
   
  $consulta_paquetes = 
             'SELECT nombre, version, fecha, link_software '.
             'FROM paquetes_software WHERE id_software='.$_GET['ids'].
             $ordenacion;

  // realiza consulta de paquetes relacionados con software
  $resultado = mysql_query($consulta_paquetes, $conexion);  
  $numero_filas = mysql_num_rows($resultado);
  
  // chequea si hay un  miembro perteneciente al proyecto
  if ($numero_filas > 0)
  {
     while ($paquete = mysql_fetch_row($resultado))
     {
       // obtiene el nombre del fichero de link
       $inicio = strrpos($paquete[3],'/');
       $nombre_fichero = substr($paquete[3],$inicio+1);

       $lista_valores = array(
             'NOMBRE' => $paquete[0],
             'VERSION' => $paquete[1],
             'FECHA' => $paquete[2],
             'LINK_SW' => $paquete[3],
             'NOMBRE_SW' => $nombre_fichero);
       // asigna valores a la pagina
       $pagina->assign("LISTA", $lista_valores);
       $pagina->parse("main.tabla_ficha.lista_paquetes.fila");
     }
     // imprime tabla paquetes
     $pagina->assign("IDS", $_GET['ids']);
     $pagina->parse("main.tabla_ficha.lista_paquetes");
  }  
  
  $pagina->parse("main.tabla_ficha");
   
  // cierra descriptor
  mysql_close($conexion);

  //imprime resultado
  $pagina->parse("main");
  $pagina->out("main"); 

?>