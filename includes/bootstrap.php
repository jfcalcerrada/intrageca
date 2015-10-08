<?php

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

require_once __DIR__ . '/php5/http_build_url.php';
require_once __DIR__ . '/api/url.php';
require_once __DIR__ . '/api/api.php';
require_once __DIR__ . '/xtemplate.php';

// TODO mover
require_once __DIR__ . '/../common/common_error.php';

require_once __DIR__ . '/config.php';


session_start();
require_once __DIR__ . '/language.php';
require_once __DIR__ . '/session.php';


//
require_once __DIR__ . '/access.php';

// Carla la clase de la base de datos
require_once __DIR__ . '/db.php';


// Obtiene el idioma
$_lang = $_SESSION['lang'];

// Obtiene el nombre de archivo
$_file = basename($_SERVER['SCRIPT_NAME'], '.php');