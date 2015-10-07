<?php

/**
 *
 */


// Carga los includes de la cabecera
require_once 'includes/initialize.php';

// Carga el modelo
require_once 'model/includes/miembros.php';
require_once 'model/' . $_file . '.php';


/**
 * miembro_editar.php
 *
 * Genera el formulario con los datos del miembro para poder modificarlos.
 * Para ello usa como parámetro el Identificador de Miembro.
 * La fecha de incorporación sólo es modificable por el Administrador.
 * @access  Privado El mismo Miembro o Administrador
 * @param   integer id_miembro  Identificador del Miembro
 *
 */
//print_r($_REQUEST);

// Extrae las variables necesarias para el script
extract(arrayKeys($_REQUEST, array('id_miembro', 'idioma')));


// Obtiene el id_miembro y verifica si es correcto
$id_miembro = validateId($id_miembro);


// Controla el acceso a la pagina
accessOwnMember($id_miembro);


// Si hay cambio de idioma, lo recibe por GET
if (isset($_GET['idioma'])) {

    // Muestra error si la cadena está vacía
    if (empty($idioma)) {
        error();

    } elseif (!isset($_languages[$idioma])) {
        // Si no existe el ídioma muestra error
        error();
    }

} else {
    // Obtiene el idioma si no ha cambiado
    $idioma = (isset($_languages[$idioma])) ? $idioma : $_lang;
}


// Obtiene los datos del miembro
$miembro = getMiembroDatos($id_miembro, $idioma);
// Le añade el idioma
$miembro['idioma'] = $idioma;


// Si el id_miembro es 0, se esta insertando uno nuevo
if ($id_miembro == 0) {
    // Elimina el nombre
    $miembro['nombre'] = '';
    // Añade la fecha del día actual
    $miembro['fecha_incorporacion'] = date('Y-m-d');
}


// Si recibe miembro por POST, se ha mandado el formulario
if (isset($_POST['id_miembro'])) {


    // Comprueba el nombre
    if (!empty($_POST['nombre'])) {
        $miembro['nombre'] = $_POST['nombre'];

    } else {
        $error['nombre'] = 'El campo no puede estar vacío';
    }


    // Comprueba los apellidos
    if (!empty($_POST['apellidos'])) {
        $miembro['apellidos'] = $_POST['apellidos'];

    } else {
        $error['apellidos'] = 'El campo no puede estar vacío';
    }


    // Comprueba la foto, si se ha subido correctamente
    if ($_FILES['foto']['error'] == 0) {

        // Si la foto tiene un tamaño menor a 100KB y existe el tipo mime
        if (($_FILES['foto']['size'] < $_files['fotos']['size'])
            && (strpos($_files['fotos']['mime'], $_FILES['foto']['type']) !== false)
        ) {
            // Obtiene la extensión de la imagen
            $extension = pathinfo($_FILES['foto']['name']);
            $extension = $extension['extension'];

            // Crea el enlace
            $miembro['link_foto'] = $_files['fotos']['dir'] . '/'
                                  . 'f' . $id_miembro . '.' . $extension;


            // Guarda la imagen
            copy($_FILES['foto']['tmp_name'], $miembro['link_foto']);

        } else {
            // Si la foto no cumple con los requisitos anteriores
            $error['foto'] = 'Imagen no válida';
        }
    }


    // TODO: añadir posibilidad de eliminar la foto



    // Comprueba la categoria
    if (isset($_member['grupos'][$_POST['categoria']])) {
        $miembro['categoria'] = $_POST['categoria'];

    } else {
        $error['categoria'] = 'Categoría no válida';
    }


    // Comprueba el puesto
    if (!empty($_POST['puesto'])) {
        $miembro['puesto'] = $_POST['puesto'];

    } else {
        $error['puesto'] = 'Por favor rellene el campo';
    }

    // Comprueba la afiliación
    if (!empty($_POST['afiliacion'])) {
        $miembro['afiliacion'] = $_POST['afiliacion'];

    } else {
        $error['afiliacion'] = 'Por favor rellene el campo';
    }


    // Si la fecha es la misma
    if ($miembro['fecha_incorporacion'] == $_POST['fecha_incorporacion']) {
        // Si es administrador
        if ($_SESSION['privilegios'] == ADMIN) {

            // Si la fecha es valida
            if (checkdate($_POST['mes'], $_POST['dia'], $_POST['anyo'])) {
                $miembro['fecha_incorporacion'] = $_POST['anyo'] . '-'
                                                . $_POST['mes']  . '-'
                                                . $_POST['dia'];

            } else {
                $error['fecha_incorporacion'] = 'Error en la fecha';
            } // fecha valida
        } // admin

    } else {
        $error['fecha_incorporacion'] = 'Error en la fecha';
    } // misma fecha


    // Comprueba la direccion
    if (!empty($_POST['direccion'])) {
        $miembro['direccion'] = $_POST['direccion'];

    } else {
        $error['direccion'] = 'Por favor rellene el campo';
    }


    // Comprueba el telefono
    if (!empty($_POST['telefono'])) {
        // Si cumple la expresion regular, digitos, espacio, mas y paréntesis
        if (preg_match('/^[\d\s+()]{9,20}$/', $_POST['telefono'])) {
            $miembro['telefono'] = $_POST['telefono'];

        } else {
            $error['telefono'] = 'Campo no válido';
        }

    } else {
        $error['telefono'] = 'Por favor rellene el campo';
    }


    // Comprueba el fax
    if (!empty($_POST['fax'])) {
        // Mismo patrón que para el telefono
        if (preg_match('/^[\d\s+()]{9,20}$/', $_POST['fax'])) {
            $miembro['fax'] = $_POST['fax'];

        } else {
            $error['fax'] = 'Campo no válido';
        }

    } else {
        $error['fax'] = 'Por favor rellene el campo';
    }


    // Reg exp para el email
    $regmail = '/^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@'
             . '[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/';

    // Si la direccion de mail es valida
    if (preg_match($regmail, $_POST['email'])
    ) {
        $miembro['email'] = $_POST['email'];

    } else {
        $error['email'] = 'Campo no válido';
    }


    // Comprueba la foto, si se ha subido correctamente
    if ($_FILES['cv']['error'] == 0) {

        // Si la foto tiene un tamaño menor a 3MB y existe el tipo mime
        if (($_FILES['cv']['size'] < $_files['curriculums']['size'])
            && (strpos($_files['curriculums']['mime'], $_FILES['cv']['type']) !== false)
        ) {
            // Obtiene la extensión de la imagen
            $extension = pathinfo($_FILES['cv']['name']);
            $extension = $extension['extension'];

            // Crea el enlace
            $miembro['link_curriculum'] =
                  $_files['curriculums']['dir'] . '/'
                . 'c' . $id_miembro . '_' . $idioma . '.' . $extension;


            // Guarda la imagen
            copy($_FILES['cv']['tmp_name'], $miembro['link_curriculum']);

        } else {
            $error['cv'] = 'Currículum no válido';
        }
    }


    // Actualiza los datos del miembro
    $miembro['id_miembro'] = setMiembroDatos($miembro);


    // Mensaje para mostrar sobre el resultado de la accion
    $mensaje = (empty($error))
             ? 'Los datos se han actualiazdo correctamente'
             : 'Se han actualizado solamente parte de los datos';
}


// Extrae los valores de la fecha de entrada del miembro
$miembro['anyo'] = strtok($miembro['fecha_incorporacion'], '-');
$miembro['mes']  = strtok('-');
$miembro['dia']  = strtok('-');


// Si es activo lo cambia a checked
$miembro['activo'] = ($miembro['activo']) ? 'checked="checked"' : '';


// Incluye la vista de la pagina
require_once 'vista/' . $_file . '.php';


?>
