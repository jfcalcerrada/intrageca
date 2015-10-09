<?php

require_once 'common/init.php';


/**
 * @name administracion.php
 *
 * @desc
 *
 * @access Administrador
 *
 */

// Controla el acceso
if ($_SESSION['privilegios'] != ADMIN) {
  error($errors['privilegios'], 'No tiene privilegios para acceder', 'miembros.php');
}

// Parsea el contenido
$_content->parse("content");
require_once __DIR__ . '/includes/layout.php';
