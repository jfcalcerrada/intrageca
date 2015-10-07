<?php

/**
 * Genera una pagina con la lista de los miembros de grupo ordenados por
 * categor�as, por activo/no activo y por orden alfab�tico del apellido.
 * Cada miembro es, a su vez, un enlace a su ficha personal.
 * En caso de acceder como invitado, la lista de los usuarios "No activos" no se
 * muestra.
 *
 * @access  P�blico   Para los miembros "Activos"
 * @access  Privado   Para los miembros "Activos" y "No activos"
 *
 * @param   Sin par�metros
 *
 * @var $_member['grupos'] Definicion de los grupos de los miembros
 */


// Carga los includes de la cabecera
require_once 'includes/initialize.php';

// Carga el modelo
require_once 'model/' . $_file . '.php';



// Si es un invitado s�lo puede ver los miembros activos
$activo = ($_SESSION['privilegios'] == INVITADO) ? 1 : 0;

// Obtiene todos los miembros de todos los grupos
$miembros = getMiembros($activo, $_lang, $_member['grupos']);


// Incluye la vista de la pagina
require_once 'vista/' . $_file . '.php';

?>