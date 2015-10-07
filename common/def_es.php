<?php
// Idiomas disponibles en español
$gen_idiomas_disp = array(
    'es' => 'Español',
    'us' => 'Inglés');

// Titulos de la cabecera de la WEB
$nombres_web = array(
    'grupo' => 'Grupo de Radiofrecuencia (GRF)',
    'dpto'  => 'Departamento de Teoría de la Señal y Comunicaciones',
    'univ'  => 'Universidad Carlos III de Madrid');

// Definición de los titulos de las webs
$titulos_web = array(  
    'colaboradores'       => 'Colaboradores del grupo',
    'miembro_editar'      => 'Editar Miembro',
    'miembro_ver_cur'     => 'Curriculum de Miembro',
    'miembro_ver_ficha'   => 'Información de Miembro',
    'miembros'            => 'Miembros del Grupo',
    'presentacion'        => 'Presentación del Grupo',
    'proyecto_ver_ficha'  => 'Información de Proyecto',
    'proyectos'           => 'Proyectos del Grupo',
    'public_busqueda'     => 'Resultado Búsqueda de Publicaciones',
    'publicaciones'       => 'Publicaciones del Grupo',
    'software'            => 'Software del Grupo',
    'software_descarga'   => 'Descarga de Software',
    'software_ver_ficha'  => 'Información de Software',
    'commom_error'        => 'Mensaje de Error',
    'commom_mensaje'      => 'Mensaje de Aviso');

// Roles de la intrageca
$gen_roles = array(
    'Usuario'  => 'Usuario',
    'Invitado' => 'Invitado');

// Definicion de los grupos dentro de los miembros y su
// relación con las etiquetas mostradas para cada grupo dentro
// del web.
$mbr_rel_grupos = array(
    'TITULAR' => 'Profesor/Investigador',
    'BEC_DOC' => 'Becario de Doctorado',
    'BEC_GRA' => 'Becario de Grado');

// definicion de tipos de monedas posibles
$proy_tipos_monedas = array(
    'EUROS'   => 'Euros',
    'DOLARES' => 'Dolares',
    'PTAS'    => 'Pesetas',
    'OTRO'    => ' ');

// definicion de estados de proyecto
$proy_estado_proyecto = array(
    0 => 'Concedido',
    1 => 'En curso',
    2 => 'Terminado');

// definicion de lógica de busqueda
$public_logica_busqueda = array(
    'OR'  => 'al menos uno de',
    'AND' => 'todos');

// definicion de los tipos de publicaciones
$public_tipos_refer = array ( 
    'LIBROS'      => 'Libros/Cap. Libro',
    'REVISTAS'    => 'Revistas',
    'CONGRESOS'   => 'Congresos',
    'COLECCIONES' => 'Colecciones',
    'PATENTES'    => 'Patentes',
    'TESIS'       => 'Tesis Doctoral',
    'PFC'         => 'Proyecto Fin de Carrera',
    'OTROS'       => 'Otros');

// -- Traslacion de campos de ingles a español
$public_traduc_campos = array(
    'author'    => 'autor',               'title'       => 'título',
    'chapter'   => 'capítulo',            'year'        => 'año',
    'month'     => 'mes',                 'pages'       => 'páginas',
    'edition'   => 'edición',             'editor'      => 'editor' ,
    'booktitle' => 'título del libro',    'series'      => 'serie',
    'ISBN'      => 'ISBN',                'journal'     => 'revista',
    'number'    => 'número',              'volume'      => 'volumen',
    'publisher' => 'editorial',           'institution' => 'institución',
    'organization' => 'organización',     'address'     => 'dirección',
    'note'      => 'nota');

// Traslacion de los meses a su nombre largo en idioma correspondiente
$public_nombre_meses = array(
    'jan' => 'Enero',   'feb' => 'Febrero',   'mar' => 'Marzo',
    'apr' => 'Abril',   'may' => 'Mayo',      'jun' => 'Junio',
    'jul' => 'Julio',   'aug' => 'Agosto',    'sep' => 'Septiembre',
    'oct' => 'Octubre', 'nov' => 'Noviembre', 'dec' => 'Diciembre');


// definicion de mensajes de error
$errors = array(
    'conexion'      => 'El servidor está temporalmente fuera de servicio',
    'usuario'       => 'Usuario o contraseña no válidos',
    'introducir'    => 'Debe introducir un usuario y clave para acceder a la '.
                       'intranet',
    'enlace'        => 'Enlace inválido',
    'identificador' => 'El identificador introducido no es válido',
    'privilegios'   => 'No tiene permiso para acceder a la página',
    'consulta'      => 'Se ha producido un error en la consulta',
    'actualiza'     => 'Se ha producido un error durante la actualizacion',
    'asignatura'    => 'Asignatura desconocida',
    'miembro'       => 'Miembro desconocido',
    'proyecto'      => 'Proyecto desconocido',
    'linea'         => 'Línea de investigación desconocida',
    'software'      => 'Software desconocido',
    'prueba'        => 'Esto es una prueba para ver donde sale');

$gen_enlace_invalido  = "Enlace inválido";
$gen_separador_campos = " y ";
$gen_error_conexion   = "El servidor está temporalmente fuera de servicio.";
$mbr_usuario_desc     = "Miembro desconocido";
$pry_proyecto_desc    = "Proyecto desconocido";
$sft_software_desc    = "Software desconocido";
// mensajes de activacion
$sft_clave_repetida   = "Las claves introducidas no son iguales";
$sft_usuario_yareg    = "El usuario ya ha sido dado de alta";
$sft_usuario_registrado = "El usuario ha sido registrado";
$sft_usuario_activado   = "El usuario ha sido activado";
$sft_subject_act = "Activación de cuenta de descarga Software";
$sft_mensaje_act = "Se ha recibido una petición de activación de cuenta".
    " de esta direccion de correo para el sitio del grupo GECA.\n".
    " Si desea activar la cuenta, visite el siguiente enlace:\n".
    " http://".$_SERVER['SERVER_NAME']."/webgeca/software_activar.php?";

?>