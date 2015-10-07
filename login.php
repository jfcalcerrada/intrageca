<?php

/**
 * Página de Login
 *
 * @access Público Todos pueden entrar a registrarse
 *
 * @param string $usuario  Nombre del usuario
 * @param string $password Contraseña del Usuario
 *
 */


// Carga los includes de la cabecera
require_once 'includes/initialize.php' ;

// Carga el modelo
require_once 'model/' . $_file . '.php' ;


// Extrae las variables necesarias para el script
extract(arrayKeys($_REQUEST, array('usuario', 'password')));


// Si se ha enviado el formulario, existe $usuario
if (isset($_POST['usuario'])) {

    // Comprueba que se hayan introducido los datos
    if (empty($usuario) || empty($password)) {
        error('introducir');
    }

    // Comprueba que se hayan introducido caracteres válidos
    // o el usuario tiene menos de 4 o más de 16 caracteres
    // o la passwrod tiene menos de 4 o más de 20 caracteres
    //  /^[a-zA-Z0-9_-]+$/  =>  /^[\w\d-]+$/
    if (!(preg_match('/^[\w\d-]+$/', $usuario))
        || ((strlen($usuario) < 4)  || (strlen($usuario) > 16))
        || ((strlen($password) < 4) || (strlen($password) > 20))
    ) {
        error('usuario');
    }


    // Obtenemos los datos de la session
    $session = getMiembroAutentica($usuario, $password);


    // Añade a la session los datos del registro
    $_SESSION = array_merge($_SESSION, $session);

    //header('Location: miembro_ver_ficha.php?id_miembro'.$_SESSION['id_miembro']);
}


// Incluye la vista de la pagina
require_once 'vista/' . $_file . '.php';


?>
