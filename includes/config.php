<?php

// Listado de idiomas disponibles en la web
$_languages = array(
    'es'  => '',
    'us'  => '');

// Tiempo máximo por session, en segundos
define('TIEMPO_SESSION', 3600);

// Definicion de usuarios y privilegios
define('INVITADO', 0);
define('ADMIN',    1);
define('MIEMBRO',  2);

// Password por defecto para los nuevos usuarios
define('PASSWORD_DEFECTO', 'defecto6R#');


// Enlaces asociados a la parte superior de la página
$_titles['links'] = array(
    'grupo'         => 'presentacion.php',
    'departamento'  => 'http://www.tsc.uc3m.es',
    'universidad'   => 'http://www.uc3m.es');

// Directorio y tipos aceptados para las fotos
$_files['fotos'] = array(
    'dir'   => 'fotos',
    'size'  => 300000,
    'mime'  => 'image/jpeg image/jpg image/png');

// Directorio y tipos aceptados para los currículums
$_files['curriculums'] = array(
    'dir'   => 'docs/cv',
    'size'  => 1000000,
    'mime'  => 'application/pdf application/msword application/rtf');

// Directorio y tipos aceptados para llos
$_files['publicaciones'] = array(
    'dir'   => 'docs/public',
    'mime'  => 'application/pdf');


// Old config file!

// variables de acceso a Base de datos
$BASE_DATOS = "webgeca_prod";
$USER_BD    = "webgrupo";
$PASS_BD    = "webgrupo";

// Idiomas disponbiles, español el primero
$gen_idiomas_disp = $_languages;


// Enlaces de los titulos
$link_nombres = array(
    'grupo' =>  'presentacion.php',
    'dpto'  =>  'http://www.tsc.uc3m.es',
    'univ'  =>  'http://www.uc3m.es');


// Directorios donde se almacenaran las fotos de los miembros
$mbr_dir_fotos = 'fotos/';
// Tipos MIME aceptados para las fotos
$mime_fotos = 'image/jpeg image/png';

// Directorio donde se almacenaran los curriculums de los miembros
$mbr_dir_docs = "docs/cv/";
// Tipos MIME aceptados para los curriculums
$mime_cv = 'application/pdf application/msword application/rtf';

// Definición del directorio donde se almacenan los backups de las bibliografias
$public_dir_backup = "backup/";

// Definicion del directorio donde se almacenan los documentos
$public_dir_docs = "docs/public/";

// Definicion de directorio donde se almacena el software
$software_dir_paquetes = "sw/";
