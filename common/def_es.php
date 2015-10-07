<?php
// Idiomas disponibles en espa�ol
$gen_idiomas_disp = array(
    'es' => 'Espa�ol',
    'us' => 'Ingl�s');

// Titulos de la cabecera de la WEB
$nombres_web = array(
    'grupo' => 'Grupo de Radiofrecuencia (GRF)',
    'dpto'  => 'Departamento de Teor�a de la Se�al y Comunicaciones',
    'univ'  => 'Universidad Carlos III de Madrid');

// Definici�n de los titulos de las webs
$titulos_web = array(  
    'colaboradores'       => 'Colaboradores del grupo',
    'miembro_editar'      => 'Editar Miembro',
    'miembro_ver_cur'     => 'Curriculum de Miembro',
    'miembro_ver_ficha'   => 'Informaci�n de Miembro',
    'miembros'            => 'Miembros del Grupo',
    'presentacion'        => 'Presentaci�n del Grupo',
    'proyecto_ver_ficha'  => 'Informaci�n de Proyecto',
    'proyectos'           => 'Proyectos del Grupo',
    'public_busqueda'     => 'Resultado B�squeda de Publicaciones',
    'publicaciones'       => 'Publicaciones del Grupo',
    'software'            => 'Software del Grupo',
    'software_descarga'   => 'Descarga de Software',
    'software_ver_ficha'  => 'Informaci�n de Software',
    'commom_error'        => 'Mensaje de Error',
    'commom_mensaje'      => 'Mensaje de Aviso');

// Roles de la intrageca
$gen_roles = array(
    'Usuario'  => 'Usuario',
    'Invitado' => 'Invitado');

// Definicion de los grupos dentro de los miembros y su
// relaci�n con las etiquetas mostradas para cada grupo dentro
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

// definicion de l�gica de busqueda
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

// -- Traslacion de campos de ingles a espa�ol
$public_traduc_campos = array(
    'author'    => 'autor',               'title'       => 't�tulo',
    'chapter'   => 'cap�tulo',            'year'        => 'a�o',
    'month'     => 'mes',                 'pages'       => 'p�ginas',
    'edition'   => 'edici�n',             'editor'      => 'editor' ,
    'booktitle' => 't�tulo del libro',    'series'      => 'serie',
    'ISBN'      => 'ISBN',                'journal'     => 'revista',
    'number'    => 'n�mero',              'volume'      => 'volumen',
    'publisher' => 'editorial',           'institution' => 'instituci�n',
    'organization' => 'organizaci�n',     'address'     => 'direcci�n',
    'note'      => 'nota');

// Traslacion de los meses a su nombre largo en idioma correspondiente
$public_nombre_meses = array(
    'jan' => 'Enero',   'feb' => 'Febrero',   'mar' => 'Marzo',
    'apr' => 'Abril',   'may' => 'Mayo',      'jun' => 'Junio',
    'jul' => 'Julio',   'aug' => 'Agosto',    'sep' => 'Septiembre',
    'oct' => 'Octubre', 'nov' => 'Noviembre', 'dec' => 'Diciembre');


// definicion de mensajes de error
$errors = array(
    'conexion'      => 'El servidor est� temporalmente fuera de servicio',
    'usuario'       => 'Usuario o contrase�a no v�lidos',
    'introducir'    => 'Debe introducir un usuario y clave para acceder a la '.
                       'intranet',
    'enlace'        => 'Enlace inv�lido',
    'identificador' => 'El identificador introducido no es v�lido',
    'privilegios'   => 'No tiene permiso para acceder a la p�gina',
    'consulta'      => 'Se ha producido un error en la consulta',
    'actualiza'     => 'Se ha producido un error durante la actualizacion',
    'asignatura'    => 'Asignatura desconocida',
    'miembro'       => 'Miembro desconocido',
    'proyecto'      => 'Proyecto desconocido',
    'linea'         => 'L�nea de investigaci�n desconocida',
    'software'      => 'Software desconocido',
    'prueba'        => 'Esto es una prueba para ver donde sale');

$gen_enlace_invalido  = "Enlace inv�lido";
$gen_separador_campos = " y ";
$gen_error_conexion   = "El servidor est� temporalmente fuera de servicio.";
$mbr_usuario_desc     = "Miembro desconocido";
$pry_proyecto_desc    = "Proyecto desconocido";
$sft_software_desc    = "Software desconocido";
// mensajes de activacion
$sft_clave_repetida   = "Las claves introducidas no son iguales";
$sft_usuario_yareg    = "El usuario ya ha sido dado de alta";
$sft_usuario_registrado = "El usuario ha sido registrado";
$sft_usuario_activado   = "El usuario ha sido activado";
$sft_subject_act = "Activaci�n de cuenta de descarga Software";
$sft_mensaje_act = "Se ha recibido una petici�n de activaci�n de cuenta".
    " de esta direccion de correo para el sitio del grupo GECA.\n".
    " Si desea activar la cuenta, visite el siguiente enlace:\n".
    " http://".$_SERVER['SERVER_NAME']."/webgeca/software_activar.php?";

?>