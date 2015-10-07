<?php

// Idiomas disponibles traducidos al espa�ol
$_languages = array(
    'es' => 'Espa�ol',
    'us' => 'Ingl�s',
    'fr' => 'Franc�s');

// Roles de la intrageca
$_roles = array(
    0 => 'Invitado',
    1 => 'Administrador',
    2 => 'Miembro');

// Nombres asociados a la parte superior de la p�gina
$_titles['nombres'] = array(
    'grupo'         => 'Grupo de Radiofrecuencia (GRF)',
    'departamento'  => 'Departamento de Teor�a de la Se�al y Comunicaciones',
    'universidad'   => 'Universidad Carlos III de Madrid');

// T�tulos asociados a las distintas p�ginas
$_titles['titulos'] = array(
    'colaboradores'       => 'Colaboradores del grupo',
    'login'               => 'Acceso a la Intranet',
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

// Tipos l�gica de busqueda
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
    'author'      => 'autor',             'title'       => 't�tulo',
    'chapter'     => 'cap�tulo',          'year'        => 'a�o',
    'month'       => 'mes',               'pages'       => 'p�ginas',
    'edition'     => 'edici�n',           'editor'      => 'editor' ,
    'booktitle'   => 't�tulo del libro',  'series'      => 'serie',
    'ISBN'        => 'ISBN',              'journal'     => 'revista',
    'number'      => 'n�mero',            'volume'      => 'volumen',
    'publisher'   => 'editorial',         'institution' => 'instituci�n',
    'organization'=> 'organizaci�n',      'address'     => 'direcci�n',
    'note'        => 'nota');

// Traducciones de los meses
$_months = array(
    'jan' => 'Enero',   'feb' => 'Febrero',   'mar' => 'Marzo',
    'apr' => 'Abril',   'may' => 'Mayo',      'jun' => 'Junio',
    'jul' => 'Julio',   'aug' => 'Agosto',    'sep' => 'Septiembre',
    'oct' => 'Octubre', 'nov' => 'Noviembre', 'dec' => 'Diciembre');


// Mensajes de error
$_error = array(
    'conexion'      => 'El servidor est� temporalmente fuera de servicio',
    'usuario'       => 'Usuario o contrase�a no v�lidos',
    'introducir'    => 'Debe introducir un usuario y clave para acceder a '
                       . 'la intranet',
    'enlace'        => 'Enlace inv�lido',
    'identificador' => 'El identificador introducido no es v�lido',
    'privilegios'   => 'No tiene permiso para acceder a la p�gina',
    'consulta'      => 'Se ha producido un error en la consulta',
    'actualiza'     => 'Se ha producido un error durante la actualizacion',
    'asignatura'    => 'Asignatura desconocida',
    'miembro'       => 'Miembro desconocido',
    'proyecto'      => 'Proyecto desconocido',
    'linea'         => 'L�nea de investigaci�n desconocida',
    'software'      => 'Software desconocido');

?>