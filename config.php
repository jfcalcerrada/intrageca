<?php
// variables de acceso a Base de datos
$BASE_DATOS = "webgeca_prod";
$USER_BD    = "webgrupo";
$PASS_BD    = "webgrupo";

// Idiomas disponbiles, español el primero
$gen_idiomas_disp = array ( 'es' => '',
                            'us' => '');

// Definiciones de Privilegios
define('ADMIN', 1);
define('INVITADO', 0);

// Tiempo máximo por session, en segundos
define('TIEMPO_SESSION', 600);

// Enlaces de los titulos
$link_nombres = array(
  'grupo' =>  'presentacion.php',
  'dpto'  =>  'http://www.tsc.uc3m.es',
  'univ'  =>  'http://www.uc3m.es');

// Enlace a la Intranet
$intranet = "https://ls6.tsc.uc3m.es";

// Password por defecto para nuevos usuarios
define('PASSWORD_DEFECTO', 'defecto6R#');

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

?>