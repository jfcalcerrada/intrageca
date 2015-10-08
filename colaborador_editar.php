<?php

require_once __DIR__ . '/common/init.php';

// ejecuta autenticacion antes que nada
autenticar_usuario();
require_once "colaborador_insertar.php";

//--------------------------------------------------------------------------
// colaboradores.php
//
// Genera la página principal de colaboradores y los exporta al template
// colaboradores.html. Se genera una lista de grupos de colaboracion, cada
// uno de los cuales contiene una serie de miembros y proyectos en los
// que colabora.
//--------------------------------------------------------------------------

  // declara arrays a usar
  $lista_id_miembros = array();
  
  // crea parser de la página
  $pagina=new XTemplate ("templates/es/colaborador_editar.html");

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
 // VERIFICA SI TIENE QUE INSERTAR/ACTUALIZAR REGISTRO TRAS AUTOLLAMADA
 //--------------------------------------------------------------------
 // le hemos dado a actualizar al formulario, modificando valores
 if ($_POST['modificado'] == 1) 
 {
   // llama a la funcion de insertar/actualizar
   $id_grupo = colaborador_insertar($conexion, $_POST);
 }
 // le hemos dado a actualizar al formulario, sin modificar valores
 else if (strlen($_POST['idc']) > 0) // 
 {
   $id_grupo = $_POST['idc'];
 } 
 // para el caso de un enlace a la página de editar
 else
 {
   $id_grupo = $_GET['idc'];
 }
 
 // verifica que tras identificacion, tenemos un identificado valido
 if (strlen($id_grupo)==0) 
 {
        ERR_muestra_pagina_error("Grupo desconocido","");
        exit;     
 }  
 //--------------------------------------------------------------------
 // RELLENA FORMULARIO DE BORRADO SI NO ES NUEVO
 //-------------------------------------------------------------------- 
 if ($id_grupo != 0)
 {
  $pagina->assign("IDC", $id_grupo);
  $pagina->parse("main.form_borrar");
 }
 //--------------------------------------------------------------------
 // CONSULTA DE COLABORADORES
 //--------------------------------------------------------------------   

 if ($id_grupo != 0)
 {
    // definicion de consultas de la base de datos
    $consulta_grupos ='SELECT id_grupo, nombre_grupo, descripcion, link_grupo,'. 
        'publico FROM grupos_colaboradores WHERE id_grupo='.$id_grupo;
     
    // realiza consulta para ver campos distintos
    $resultado = mysql_query($consulta_grupos, $conexion);
    // verifica que se ejecuto bien
    if (!$resultado)
    { 
      echo "Error en consulta ".$consulta_grupos;
      exit;
    }
    
    // imprime cada grupo
    $grupos = mysql_fetch_row($resultado);
 }
 // verifica si tenemos que poner el checkbox de publico
 $publico = ($grupos[4] == 1)? "CHECKED":"";
 
 // asigna valores de grupo
 $lista_valores = array ( 
                    'ID_GRUPO' => $id_grupo,
                    'GRUPO_COLABORADOR' => $grupos[1],
                    'DESCRIPCION' => $grupos[2],
                    'LINK_GRUPO' => $grupos[3],
                    'PUBLICO' => $publico);
 // imprimelos en página
 $pagina->assign("LISTA",$lista_valores);
 $pagina->parse("main.form_colaboradores.cabecera");
 
 //-------------------------------------
 // obtiene todos los miembros del grupo
 //-------------------------------------
 if ($id_grupo != 0)
 {
    $consulta_miembros = 'SELECT nombre, puesto, email_colaborador, '.
       'link_colaborador, id_colaborador, director FROM colaboradores '.
       'WHERE grupo_pertenece='.$grupos[0];
    
    // realiza consulta para ver campos distintos
    $resultado2 = mysql_query($consulta_miembros, $conexion);
    
    // verifica que se ejecuto bien
    if (!$resultado)
    { 
      echo "Error en consulta ".$consulta_miembros;
      exit;
    }  
    // inicializa contador de miembros
    $numero_miembros = 0;  
    //imprime para cada miembro del grupo
    while ($miembro = mysql_fetch_row($resultado2))
    {
      // incrementa el numero de miembro
      $numero_miembros = $numero_miembros + 1;
      $checked = ($miembro[5] == 1)?  "CHECKED":"";      
   
      // asigna valores de grupo
      $lista_valores = array ( 
                'MI_BORRAR' => 'mi_borrar_'.$numero_miembros,
                'MI_NOMBRE' => 'mi_nombre_'.$numero_miembros,
                'NOMBRE' => $miembro[0],
                'MI_PUESTO' => 'mi_puesto_'.$numero_miembros,
                'PUESTO' => $miembro[1],
                'MI_EMAIL' => 'mi_email_'.$numero_miembros,
                'EMAIL' => $miembro[2],
                'MI_LINK' => 'mi_link_'.$numero_miembros,
                'LINK' => $miembro[3],
                'GC_ID_MI' => 'mi_id_mie_'.$numero_miembros,
                'ID_MI' => $miembro[4],
                'MI_DIRECTOR' => 'mi_dir_'.$numero_miembros,
                'ACTIVO' => $checked);
      // imprimelos en página
      $pagina->assign("LISTA",$lista_valores);
      $pagina->parse("main.form_colaboradores.colaboradores.fila");            
    }

    // asigna valores de grupo
    $lista_valores = array(
            'VAL_MIEMBROS' => $numero_miembros);
    // imprime cabecera de colaboradores
    $pagina->assign("LISTA",$lista_valores);
    $pagina->parse("main.form_colaboradores.colaboradores"); 
 }   
 // cierra tabla
 $pagina->parse("main.form_colaboradores");
 // cierra descriptor
 mysql_close($conexion);

 //imprime resultado
 $pagina->parse("main");
 $pagina->out("main"); 
