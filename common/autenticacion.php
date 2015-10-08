<?

/* Funcion que autentica al usuario
 *
 *  Esta funcion chequea si hay una sesion abierta, si no la hay, pide
 *  al usuario la autenticacion para poder darle una sesion válida que
 *  haga posible el acceso del usuario al web
 */

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
