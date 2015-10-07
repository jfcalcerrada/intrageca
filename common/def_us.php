<?php
// Idiomas disponibles en español
$gen_idiomas_disp = array ( 'es' => 'Spanish',
                            'us' => 'English');

// Titulos de la cabecera de la WEB
$nombres_web = array('grupo' => 'Radio Frequency Group (GRF)',
                     'dpto'  => 'Department of Signal Theory and Communications',
                     'univ'  => 'Universidad Carlos III de Madrid');

// Definición de los titulos de las webs
$titulos_web = array (
    'colaboradores'     => 'Colaboradores del grupo',
    'miembro_editar'    => 'Editar Miembro',
    'miembro_ver_cur'   => 'Curriculum de Miembro',
    'miembro_ver_ficha' => 'Información de Miembro',
    'miembros'          => 'Miembros del Grupo',
    'presentacion'      => 'Presentación del Grupo',
    'proyecto_ver_ficha' => 'Información de Proyecto',
    'proyectos'         => 'Proyectos del Grupo',
    'public_busqueda'   => 'Resultado Búsqueda de Publicaciones',
    'publicaciones'     => 'Publicaciones del Grupo',
    'software'          => 'Software del Grupo',
    'software_descarga' => 'Descarga de Software',
    'software_ver_ficha' => 'Información de Software',
    'commom_error'      => 'Mensaje de Error',
    'commom_mensaje'    => 'Mensaje de Aviso');

// Roles de la intrageca
$gen_roles = array ('Usuario'  => 'User',
                    'Invitado' => 'Guest');

// Definicion de los grupos dentro de los miembros y su
// relación con las etiquetas mostradas para cada grupo dentro
// del web.

$mbr_rel_grupos = array ( 'TITULAR' => 'Profesor/Investigator', 
                          'BEC_DOC' => 'Thesis fellow', 
                          'BEC_GRA' => 'Degree fellow');

// definicion de tipos de monedas posibles
$proy_tipos_monedas = array ('EUROS'   => 'Euros',
                             'DOLARES' => 'Dolars',
                             'PTAS'    => 'Pesetas',
                             'OTRO'    => ' ');

// definicion de estados de proyecto
$proy_estado_proyecto = array ( 0 => 'Granted',
                                1 => 'In progress',
                                2 => 'Finished');

// definicion de lógica de busqueda
$public_logica_busqueda = array ( 'OR'  => 'at least one of',
                                  'AND' => 'all');

// definicion de los tipos de publicaciones
$public_tipos_refer = array ( 
         'LIBROS'      => 'Books/InBooks',
         'REVISTAS'    => 'Articles',
         'CONGRESOS'   => 'Conferences',
         'COLECCIONES' => 'Collections',
         'PATENTES'    => 'Patents',
         'TESIS'       => 'PHD Thesis',
         'PFC'         => 'Masters Thesis',
         'OTROS'       => 'Others');

// Traslacion de campos de ingles a ingles
// mantenida para compatibilidad de los scripts con resto de idiomas
$public_traduc_campos = array (
      'author' => 'author',              'title' => 'title', 
      'chapter' => 'chapter',            'year' => 'year', 
      'month' => 'month',                'pages' => 'pages', 
      'edition' => 'edition',            'editor' => 'editor' , 
      'booktitle' => 'booktitle',        'series' => 'series', 
      'ISBN' => 'ISBN',                  'journal' => 'journal', 
      'number' => 'number',              'volume' => 'volumen',
      'publisher' => 'publisher',        'institution' => 'institution', 
      'organization' => 'organization',  'address' => 'address',
      'note' => 'note');

// Traslacion de los meses a su nombre largo en idioma correspondiente
$public_nombre_meses = array (
 "jan" => 'January',   "feb" => 'February', "mar" => 'March', "apr" => 'April', 
 "may" => 'May',       "jun" => 'June',     "jul" => 'July',  "aug" => 'August', 
 "sep" => 'September', "oct" => 'October',  "nov" => 'November', 
 "dec" => 'December');

// definicion de mensajes de error
$errors = array(
    'conexion'   => 'The server is temporarily unavailable',
    'usuario'    => 'Username or password invalid',
    'introducir' => 'You must enter a username and password to access the '.
                        'intranet',
    'enlace'     => 'Link invalid',
    'identificador' => 'The id is invalid',
    'privilegios'=> 'No permission to access the page',
    'consulta'   => 'There was an error in the query',
    'actualiza'  => 'There was an error during the upgrade',
    'miembro'    => 'Unknown Member',
    'proyecto'   => 'Unknown Project',
    'linea'      => 'Unknown Research Topic',
    'software'   => 'Unknown Software',
    'prueba'     => 'Esto es una prueba para ver donde sale');

$gen_enlace_invalido = "Invalid link";
$gen_separador_campos = " and ";
$gen_error_conexion  = "The server is temporarily unavailable.";
$mbr_usuario_desc    = "Unknown member";
$pry_proyecto_desc   = "Unknown Project";
$sft_software_desc   = "Unknown Software";

$sft_clave_repetida  = "The passwords introduced are not equal";
$sft_usuario_yareg   = "The User account is already registered";
$sft_usuario_registrado = "The User has been correctly registered";
$sft_usuario_activado   = "The User has been correctly activated";
$sft_subject_act = "Software download account activation";
$sft_mensaje_act = "A register order has been received for this email account ".
 " for the GECA group.\n".
 " If you want to activate the account, please visit the following address:\n";
 " http://".$_SERVER['SERVER_NAME']."/webgeca/software_activar.php?";
?>