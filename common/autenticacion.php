<?

/* Funcion que autentica al usuario
 *
 *  Esta funcion chequea si hay una sesion abierta, si no la hay, pide
 *  al usuario la autenticacion para poder darle una sesion válida que
 *  haga posible el acceso del usuario al web
 */

function autenticar_usuario()
{

  // Cargamos la variable de errores
  global $errors;

  // Verifica si tenemos sesion
  if (!isset($_SESSION['id_miembro']) && $_SESSION['id_miembro'] >= 0) {

    // Verifica si el usuario se ha intentado autenticar, y no ha introducido
    // ni usuario ni password
    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SESSION['volver_pedir'] == 1) {
      header('WWW-Authenticate: Basic realm="Intranet Grupo de RadioFrecuencia"');
      header('HTTP/1.0 401 Unauthorized');
      session_destroy();
      error($errors['introducir']);

    } else {
      // Verifica consulta para ver si datos de usuario son validos
      $consulta_acceso =
        "SELECT miembro_autentica.id_miembro, privilegios, nombre ".
        "FROM miembro_autentica, miembros ".
        "WHERE usuario_web = '{$_SERVER['PHP_AUTH_USER']}' ".
          "AND password_web = MD5('{$_SERVER['PHP_AUTH_PW']}') ".
          "AND miembro_autentica.id_miembro = miembros.id_miembro";

      // realiza consulta para ver campos distintos
      $resultado_acceso = mysql_query($consulta_acceso);

      //verifica si se realizo bien la consulta
      if (!$resultado_acceso) {
        $_SESSION['volver_pedir'] = 1;
        header('HTTP/1.0 401 Unauthorized');
        //session_destroy();
        // NOTA: crear funcion que inserte log en mysql
        // NOTA: esto es un error grave, no de no reconocer usuario
        error($errors['usuario'], "Error en la consulta: $consulta_acceso");
      }

      // Verifica si existe el usuario y solamente accede a uno
      if (mysql_num_rows($resultado_acceso) == 0 
        || mysql_num_rows($resultado_acceso) > 1) {

        $_SESSION['volver_pedir'] = 1;
        header('HTTP/1.0 401 Unauthorized');
        error($errors['usuario'], "Usuario no valido: $consulta_acceso");
      }
            
      // Obtiene el identificador y guardalo
      $acceso = mysql_fetch_array($resultado_acceso);

      // Almacena los diferentes parametros del miembro
      $_SESSION = $acceso;
      $_SESSION['ultimo_acceso'] = time();

      // Almacena no volver a pedir, ya estamos registrados
      $_SESSION['volver_pedir'] = 0;

      // Si el password es por defecto redirige a cambiar la password
      if ($_SERVER['PHP_AUTH_PW'] == PASSWORD_DEFECTO)
        header("Location: miembro_editar_web.php?idm={$acceso['id_miembro']}");
    }

  }
  
}


function cerrar_session() {

  // Borra el identificador de usuario
  $_SESSION = array();
  unset($_SESSION['id_miembro']);

  // Elimina los privilegios
  $_SESSION['privilegios'] = 0;

  // Solicita de nuevo el usuario y contraseña al volver a entrar
  $_SESSION['volver_pedir'] = 1;

}


function control_tiempo_session() {

  // Control de tiempo de session
  if (isset($_SESSION['id_miembro'])) {

    // Comprueba si no ha excedido
    if ((time()-$_SESSION['ultimo_acceso']) <= TIEMPO_SESSION) {
      // Renueva el tiempo de la session
      $_SESSION['ultimo_acceso'] = time();

    // Si lo ha excedido
    } else {
      // Termina la session
      cerrar_session();
    }
  }

}

?>