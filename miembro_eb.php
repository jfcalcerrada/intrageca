<?
// include "autenticacion.php";
// ejecuta autenticacion antes que nada
//  autenticar_usuario();
  
/* include "config.php";
include "common/def_spa.php";
include "common/common_error.php";
include "miembro_insertar.php";
*/
//--------------------------------------------------------------------------
// miembro_editar.php
//
// Genera el formulario de un miembro en cuestion. Para ello necesita que
// se le pase como parámetro la identidad de miembro. Si la identidad
// no está disponible, se muestra la página de error.
//
// Parametros de entrada
//   idm : Identidad del miembro
//--------------------------------------------------------------------------
function muestra_miembro($id_miembro,$conexion)
{
include "miembro_insertar.php";
//--------------------------------------------------------------------------
// miembro_editar.php
//
// Genera el formulario de un miembro en cuestion. Para ello necesita que
// se le pase como parámetro la identidad de miembro. Si la identidad
// no está disponible, se muestra la página de error.
//
// Parametros de entrada
//   idm : Identidad del miembro
//--------------------------------------------------------------------------

  // definicion de Config usados
  global $BASE_DATOS;
  global $USER_BD;
  global $PASS_BD;
  
  // definicion de Commons usados
  // $mbr_rel_grupos; -- tipo de miembros
  // $mbr_usuario_desc -- mensaje de usuario desconocido
  // $gen_idiomas_disp -- Idiomas disponibles

  // verifica que idioma estamos usando
  if ((isset($_POST['idioma'])) && (strlen($_POST['idioma'])>0))
  {
    $idioma = $_POST['idioma'];
  }
  else
  {
    reset($gen_idiomas_disp);
    $idioma =  key($gen_idiomas_disp);
  }
  //--------------------------------------------------------------------
  // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
  //--------------------------------------------------------------------
  // le hemos dado a actualizar al formulario, modificando valores
  if ($_POST['modificado'] == 1) 
  {
   // llama a la funcion de insertar/actualizar
   $id_miembro = miembro_insertar($conexion, $_POST);
  }
  // le hemos dado a actualizar al formulario, sin modificar valores
  else if (strlen($_POST['id_miembro']) > 0) 
  {
   $id_miembro = $_POST['id_miembro'];
  } 
  // para el caso de un enlace a la página de editar
  else
  {
   $id_miembro = $_GET['idm'];
  }
 
  // verifica que tras identificacion, tenemos un identificado valido
  if (strlen($id_miembro)==0) 
  {
        ERR_muestra_pagina_error($mbr_usuario_desc, "");
        exit;     
  } 
 
  //--------------------------------------------------------------------
  // CONSULTA DE DATOS DE MIEMBRO
  //-------------------------------------------------------------------- 
  if ($id_miembro > 0)
  { 
     // definicion de consultas de la base de datos
     $consulta_miembros ='SELECT nombre, categoria,'.
        ' direccion, telefono, fax, email, link_foto, fecha_entrada, activo '.
        ' FROM miembros  WHERE id_miembro='.$id_miembro;
     
     // realiza consulta de miembro
     $resultado = mysql_query($consulta_miembros, $conexion);  
     $numero_filas = mysql_num_rows($resultado);
     // chequea si hay un  miembro con dicho identificador
     if ($numero_filas == 0)
     {
        ERR_muestra_pagina_error($mbr_usuario_desc, "");
        exit;          
     } 
     // obtiene datos del miembro
     $miembro = mysql_fetch_row($resultado);
           
     // consulta los campos dependientes del idioma
     $consulta_idioma ='SELECT puesto, afiliacion, curriculum, link_curriculum'.
                       ' FROM miembro_idiomas WHERE idioma="'.$idioma.
                       '" AND id_miembro='.$id_miembro;
                       
     // realiza consulta de idiomas
     $resultado = mysql_query($consulta_idioma, $conexion);
     $idm_miembro = mysql_fetch_row($resultado);    
     
     // verifica si está activo o no
     $miembro_activo = ($miembro[8] == 1)? "CHECKED":""; 

     $lista_datos_personales = array(
            'IDM' => $id_miembro,
            'NOMBRE' => $miembro[0],
            'PUESTO' => $idm_miembro[0],
            'AFILIACION' => $idm_miembro[1],
            'DIRECCION' => $miembro[2],
            'TELEFONO' => $miembro[3],
            'FAX' => $miembro[4],
            'EMAIL' => $miembro[5],
            'LINK_FOTO' => $miembro[6],
            'CURRICULUM' => $idm_miembro[2],
            'LINK_CURRICULUM' => $idm_miembro[3],
            'ACTIVO' => $miembro_activo);
  }
  else if ($id_miembro == 0)
  {
    // rellena solo el valor de IDM para autollamada de formulario en blanco
    $lista_datos_personales = array('IDM' => 0);
  }
  //--------------------------------------------------------------------
  // INSERTA LOS VALORES EN PAGINA
  //--------------------------------------------------------------------    
  
  // crea parser de la página
  $pagina=new XTemplate ("templates/miembro_editar.html");

  // RELLENA FORMULARIO DE BORRADO Y RELACIONES SI NO ES NUEVO
  if ( $lista_datos_personales['IDM']  != 0)
  {
    $pagina->assign("IDM", $lista_datos_personales['IDM']);
    $pagina->parse("main.form_borrar");
    $pagina->assign("IDM", $lista_datos_personales['IDM']);
    $pagina->parse("main.tabla_ficha.acceso_bibtex");
  }
  // imprime los idiomas disponibles
  foreach($gen_idiomas_disp as $cod => $texto_idioma)
  {
     // selecciona elementos
     $selected = ($idioma==$cod)? "SELECTED":"";
     // asigna lista
     $lista_idioma = array ( 'COD_IDIOMA' => $cod,
                             'IDIOMA'     => $texto_idioma,
                             'SELECTED'   => $selected);
     // insertalo en página
     $pagina->assign('LISTA',$lista_idioma);
     $pagina->parse('main.tabla_ficha.fila_idioma');
  }
  
  // imprime las categorias 
  foreach($mbr_rel_grupos as $cat => $texto)
  {
     // selecciona elementos
     $lista_categoria = array(
                        'CAT' => $cat,
                        'TEXTO' => $texto); 
     // verifica que es la categoria actual la seleccionada
     if ($miembro[1]==$cat) $lista_categoria['SELEC']="SELECTED";
     else $lista_categoria['SELEC']="";
     
     // asigna a la página el elemento de la lista
     $pagina->assign('LISTA',$lista_categoria);
     $pagina->parse("main.tabla_ficha.fila_cat");
     
     
  }
  
  // extrae el valor de la fecha de miembro
  $anyo_miembro = strtok($miembro[7],"-");
  $mes_miembro = strtok("-");
  $dia_miembro = strtok("-");

  // imprime fechas de incorporación
  for ($dia=1; $dia<=31; $dia++)
  {
     if ("$dia" == $dia_miembro) $selected = 'SELECTED';
     else $selected = '';
     // fija siguiente día
     $lista_fecha = array (
                     'DIA' => $dia,
                     'SELEC' => $selected);
     // asigna a la página el elemento de la lista
     $pagina->assign('LISTA',$lista_fecha);
     $pagina->parse("main.tabla_ficha.fila_dia_inc");                                          
  }
  for ($mes=1; $mes<=12; $mes++)
  {
     if ("$mes" == $mes_miembro) $selected = 'SELECTED';
     else $selected = '';
     // fija siguiente día
     $lista_fecha = array (
                     'MES' => $mes,
                     'SELEC' => $selected);
     // asigna a la página el elemento de la lista
     $pagina->assign('LISTA',$lista_fecha);
     $pagina->parse("main.tabla_ficha.fila_mes_inc");                                          
  }
  for ($anyo=2000; $anyo<=2010; $anyo++)
  {
     if ("$anyo" == $anyo_miembro) $selected = 'SELECTED';
     else $selected = '';
     // fija siguiente mes
     $lista_fecha = array (
                     'ANYO' => $anyo,
                     'SELEC' => $selected);
     // asigna a la página el elemento de la lista
     $pagina->assign('LISTA',$lista_fecha);
     $pagina->parse("main.tabla_ficha.fila_anyo_inc");                                          
  }
  
  // imprime los datos personales en página
  $pagina->assign("LISTA",$lista_datos_personales);
  $pagina->parse("main.tabla_ficha");

  //imprime resultado
  $pagina->parse("main");
  $pagina->out("main"); 

}
