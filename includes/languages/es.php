<?php

// Idiomas disponibles traducidos al español
$_languages = array(
    'es' => 'Español',
    'us' => 'Inglés',
    'fr' => 'Francés');

// Roles de la intrageca
$_roles = array(
    0 => 'Invitado',
    1 => 'Administrador',
    2 => 'Miembro');

// Nombres asociados a la parte superior de la página
$_titles['nombres'] = array(
    'grupo'         => 'Grupo de Radiofrecuencia (GRF)',
    'departamento'  => 'Departamento de Teoría de la Señal y Comunicaciones',
    'universidad'   => 'Universidad Carlos III de Madrid');

// Títulos asociados a las distintas páginas
$_titles['titulos'] = array(
    'colaboradores'       => 'Colaboradores del grupo',
    'login'               => 'Acceso a la Intranet',
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
    'error'               => 'Mensaje de Error');



// Yipos de miembros existentes
$_member['grupos'] = array(
    'TITULAR' => 'Profesor/Investigador',
    'BEC_DOC' => 'Becario de Doctorado',
    'BEC_GRA' => 'Becario de Grado');

// Tipos de monedas posibles
$_project['monedas'] = array(
    'EURO'  => 'Euros',
    'DOLAR' => 'Dolares',
    'PTAS'  => 'Pesetas',
    'OTRO'  => ' ');

//  Estados de proyecto
$_project['estados'] = array(
    0 => 'Concedido',
    1 => 'En curso',
    2 => 'Terminado');

// Tipos lógica de busqueda
$_public['logica'] = array(
    'OR'  => 'al menos uno de',
    'AND' => 'todos');

// Tipos de publicaciones
$_public['tipos'] = array (
    'LIBROS'      => 'Libros/Cap. Libro',
    'REVISTAS'    => 'Revistas',
    'CONGRESOS'   => 'Congresos',
    'COLECCIONES' => 'Colecciones',
    'PATENTES'    => 'Patentes',
    'TESIS'       => 'Tesis Doctoral',
    'PFC'         => 'Proyecto Fin de Carrera',
    'OTROS'       => 'Otros');

// Campos disponibles en las publicaciones
$_public['campos'] = array(
    'author'      => 'autor',             'title'       => 'título',
    'chapter'     => 'capítulo',          'year'        => 'año',
    'month'       => 'mes',               'pages'       => 'páginas',
    'edition'     => 'edición',           'editor'      => 'editor' ,
    'booktitle'   => 'título del libro',  'series'      => 'serie',
    'ISBN'        => 'ISBN',              'journal'     => 'revista',
    'number'      => 'número',            'volume'      => 'volumen',
    'publisher'   => 'editorial',         'institution' => 'institución',
    'organization'=> 'organización',      'address'     => 'dirección',
    'note'        => 'nota');

// Traducciones de los meses
$_months = array(
    'jan' => 'Enero',   'feb' => 'Febrero',   'mar' => 'Marzo',
    'apr' => 'Abril',   'may' => 'Mayo',      'jun' => 'Junio',
    'jul' => 'Julio',   'aug' => 'Agosto',    'sep' => 'Septiembre',
    'oct' => 'Octubre', 'nov' => 'Noviembre', 'dec' => 'Diciembre');


// Mensajes de error
$_error = array(
    'conexion'      => 'El servidor está temporalmente fuera de servicio',
    'usuario'       => 'Usuario o contraseña no válidos',
    'introducir'    => 'Debe introducir un usuario y clave para acceder a '
                       . 'la intranet',
    'enlace'        => 'Enlace inválido',
    'identificador' => 'El identificador introducido no es válido',
    'privilegios'   => 'No tiene permiso para acceder a la página',
    'consulta'      => 'Se ha producido un error en la consulta',
    'actualiza'     => 'Se ha producido un error durante la actualizacion',
    'asignatura'    => 'Asignatura desconocida',
    'miembro'       => 'Miembro desconocido',
    'proyecto'      => 'Proyecto desconocido',
    'linea'         => 'Línea de investigación desconocida',
    'software'      => 'Software desconocido');

?>