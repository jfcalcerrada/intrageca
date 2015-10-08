<?php

/**
 *
 */

 // Carga los includes de la cabecera
require_once 'includes/bootstrap.php';

// Carga el modelo
require_once 'model/includes/miembros.php';
require_once 'model/' . $_file . '.php';


/**
 * @name  miembro_editar_web.php
 *
 * @desc  Página que genera un formulario con los parametros de acceso a la web
 * de un miembro: usuario y password. Si la contraseña no se ha modificado nunca,
 * al acceder a la web redirige a esta sección. Si es el Administrador o es la
 * primera vez que se cambia la contraseña, no se necesita introducir la anterior.
 * @access  Privado   El mismo Miembro o Administrador
 * @param   integer   idm   Identificador del Miembro
 * 
 */


// Extrae únicamente lo parámetrosque son aceptados en esta página
extract(arrayKeys($_REQUEST, array('id_miembro')));


// Obtiene el id_miembro y verifica si es correcto
$id_miembro = validateId($id_miembro);


// Controla el acceso a la pagina
accessOwnMember($id_miembro);



// Comprueba si hay que actualizar los datos
if (isset($_POST['id_miembro'])) {

    // SI es ADMIN no necesita la clave
    if ($_SESSION['privilegios'] == ADMIN) {
        // Puede actualizar cualquier campo
        $canUpdate = true;

    } else {
        // Si NO es ADMIN comprueba la password
        if (isset($_POST['password']) && strlen($_POST['password'])) {
            // Obtiene si la clave es correcta
            $password  = $_POST['password'];
            $canUpdate = checkPass($id_miembro, $password);
        }
    }


    // Si puede actualizar, procedemos a realizar los cambios
    if ($canUpdate) {

        // Comprueba que haya introducido usuario
        if (isset($_POST['usuario']) && strlen($_POST['usuario'])) {

            // Obtiene el nombre del usuario introducido
            $usuario = $_POST['usuario'];

            // Comprueba que tiene caracteres válidos [A-Za-z0-9-]
            if (preg_match('/^[\w\d-]+$/', $usuario)) {

                // Si está disponible, lo actualiza
                if (isUserAvailabre($id_miembro, $usuario)) {
                    // Cambia el nombre de usuario
                    updateUser($id_miembro, $usuario);

                } else {
                    $mensaje['usuario']
                        = 'El nombre de usuario no está disponible';
                } // No disponible
            } else {
                $mensaje['usuario'] = 'Caracteres no válidos';
            } // Caracteres no válidos
        } // En blanco


        // Comprueba que haya introducido usuario
        if (isset($_POST['nueva']) && strlen($_POST['nueva'])) {
            
        }


    } else {
        $mensaje['password'] = 'Contraseña no válida';
    }


}


// Controla si tiene que actualizar
$actualizar =
  (isset($_POST['actualizar']) && $_POST['actualizar'] == 1) ? true : false;


// Muestra el submenú, y si es el administrador el botón de borrar
$contenido = menu_miembros($contenido, $id_miembro);

// Mensaje de informacion
$mensaje = '';

/* ACTUALIZA LOS DIFERENTES ELEMENTOS */
// Si esta marcado para actualizar
if ($actualizar) {

  /* COMPRUEBA EL USUARIO WEB */
  if (isset($_POST['usuario_web']) && strlen($_POST['usuario_web'])) {
    // Comprueba los caracteres
    if ($_POST['usuario_web'] == addslashes($_POST['usuario_web'])) {
      // Obtiene el usuario
      $usuario_web = $_POST['usuario_web'];

      // Comprueba que no existe
      $consulta_usuario_web =
        "SELECT id_miembro ".
        "FROM miembro_autentica ".
        "WHERE id_miembro != '$id_miembro' ".
          "AND usuario_web = '$usuario_web'";


      // Ejecuta la consulta
      $resultado_usuario_web = mysql_query($consulta_usuario_web)
        or error($errors['consulta'],
          "Error en la consulta: $consulta_usuario_web");

      // Si hay alguien con ese nombre y no es el usuario
      if (mysql_num_rows($resultado_usuario_web) > 0) {
          $mensaje .= 'El nombre de usuario ya existe.<br />';
          $usuario_web = '';
      }

    } else {
      $mensaje .= 'Caracteres no permitidos en el nombre de usuario.<br />';
      $usuario_web = '';
    }
  }
  
  /* COMPRUEBA LA PASSWORD WEB */
  if (isset($_POST['password_web']) && strlen($_POST['password_web'])) {

    // Comprueba caracteres especiales
    if ($_POST['password_web'] == addslashes($_POST['password_web'])) {

      // Obtiene el password
      $password_web = $_POST['password_web'];

      // Comprueba que esta la confirmacion
      if (isset($_POST['repetir']) && strlen($_POST['repetir'])) {

        // Comprueba que es identica
        if ($password_web != addslashes($_POST['repetir'])) {
          $mensaje .= 'La contraseña deber ser la misma';
          $password_web = '';
        }

      } else {
        $mensaje .= 'Debe confirmar la contraseña.<br />';
        $password_web = '';
      }

    } else {
      $mensaje .= 'Caracteres introducidos no válidos.<br />';
      $password_web = '';
    }

  } else {
    // Si ha introducido la confirmación pero no la contraseña
    if (isset($_POST['repetir']) && strlen($_POST['repetir']))
      $mensaje .= 'No ha introducido una contraseña.<br />';

    $password_web = '';
  }


  /* MODIFICA EL USUARIO WEB */
  if ($usuario_web != '') {
    
    // Crea consulta de actualizacion
    $consulta_usuario =
      "UPDATE miembro_autentica ".
      "SET usuario_web = '$usuario_web' ".
      "WHERE id_miembro = '$id_miembro'";

    // Actualiza el nombre de usuario
    mysql_query($consulta_usuario)
      or error($errors['consulta'], "Error en la consulta: $consulta_usuario");

    if (mysql_affected_rows() == 1)
      $mensaje .= 'Actualizado correctamente el nombre de usuario<br />';
    
  }

  /* MODIFICA EL PASSWORD WEB */
  if ($password_web != '') {

    // Comprueba si existe el campo Contraseña anterior, sólo existe si no es
    // el administrador o la contraseña no es la de por defecto
    $anterior =
      (isset($_POST['anterior'])) ? $_POST['anterior'] : PASSWORD_DEFECTO;

    // Crea la consulta de actualizacion
    $consulta_password =
      "UPDATE miembro_autentica ".
      "SET password_web = MD5('$password_web') ".
      "WHERE id_miembro = '$id_miembro' ";

    // Si es el administrador, no necesita introducir la contraseña anterior
    if ($_SESSION['privilegios'] != ADMIN)
      $consulta_password .= "AND password_web = MD5('$anterior')";

    // Actualiza la contraseña
    $resultado_password = mysql_query($consulta_password)
      or error($errors['consulta'], "Error en la consulta: $consulta_password");

    if (mysql_affected_rows() == 1) {
      $mensaje .= 'Se ha actualizado la contraseña correctamente';
    } else {
      $mensaje .= 'La contraseña anterior no coincide';
    }
  }
}


/* CONSULTA EL USUARIO WEB DEL MIEMBRO */
// Crea la consulta
$consulta_usuario =
  "SELECT id_miembro, usuario_web, password_web ".
  "FROM miembro_autentica ".
  "WHERE id_miembro = '$id_miembro'";

// Realizamos la consulta y comprobamos que no da errores
$resultado_usuario = mysql_query($consulta_usuario)
  or error($errors['consulta'], "Error en la consulta: $consulta_usuario");

// Obtiene el array de usuario
$usuario = mysql_fetch_array($resultado_usuario);

// Cierra la conexion con mysql
mysql_close($conexion);


/* MUESTRA LOS VALORES EN LA PAGINA */
// Asigna los parametros
$contenido->assign('USUARIO', array_upper($usuario));

// Si la contraseña es la solicitada por defecto, no es necesario insertarla
if ($_SESSION['privilegios'] != ADMIN && $usuario['password_web'] != MD5(PASSWORD_DEFECTO))
  $contenido->parse('content.acceso.anterior');

// Parse los datos
$contenido->parse('content.acceso');


// Asigna el mensaje de actualizacion
$contenido->assign('MENSAJE', $mensaje);


/* MUESTRA LA PAGINA */
// Parsea el contenido
$contenido->parse('content');

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>