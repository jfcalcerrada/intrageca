<?php
// Inicializamos el archivo con el script
include("common/init.php");
include("autenticacion.php");
// Autenticamos al usuario
autenticar_usuario();

$pagina = $contenido;
//--------------------------------------------------------------------------
// software.php
//
// Genera la página principal de software y los exporta al template
// software.html. Se genera una lista con el enlace a su página de
// información.
//--------------------------------------------------------------------------


 //--------------------------------------------------------------------
 // CONSULTA DE PROYECTOS
 //--------------------------------------------------------------------   
  // definicion de consultas de la base de datos
  $consulta_software = 'SELECT software.id_software, titulo, descrip_corta, '.
        ' publico FROM software_idiomas LEFT JOIN software '.
        ' ON software_idiomas.id_software=software.id_software '.
        ' WHERE idioma="'.$idioma.'" ORDER BY publico DESC, titulo ASC';
  
  // realiza consulta para ver campos distintos
  $resultado = mysql_query($consulta_software, $conexion);
  $ultimo_soft = 1;
    
  // imprime para cada miembro del grupo una entrada
  while ($software = mysql_fetch_row($resultado))
  {
    if (($ultimo_soft != $software[3]) && ($software[3] == 0))
    {
      // imprime todos los publicos
      $pagina->parse("main.tabla_software");
      // imprime cabecera de no activos
      $pagina->parse("main.tabla_software.cab_desactiva");
      // actualiza el valor de proyecto
      $ultimo_soft = $software[3];
    }
    // asigna valores de miembro
    $lista_valores = array ( 
                       'IDS' => $software[0],
                       'TITULO' => $software[1],
                       'DESC_CORTA' => $software[2]);
    // imprimelos en página
    $pagina->assign("LISTA1",$lista_valores);
    $pagina->parse("main.tabla_software.fila");
  }
  
  // cierra tabla
  $pagina->parse("main.tabla_software");

  // cierra descriptor
  mysql_close($conexion);

  //imprime resultado
  $pagina->parse("main");
  $pagina->out("main"); 

?>