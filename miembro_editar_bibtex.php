<?php

/**
 *
 */


// Carga los includes de la cabecera
require_once 'includes/initialize.php';

// Carga el modelo
require_once 'model/includes/miembros.php';
require_once 'model/' . $_file . '.php';


// Extrae las variables necesarias para el script
extract(arrayKeys($_REQUEST, array('id_miembro', 'idioma')));


// Obtiene el id_miembro y verifica si es correcto
$id_miembro = validateId($id_miembro);


// Controla el acceso a la pagina
accessOwnMember($id_miembro);


// Comprueba si hay que actualizar los datos
if (isset($_POST['id_miembro'])) {

    // Si hay datos en el campo nuevo
    if (!empty($_POST['nuevo'])) {
        insertBibtex($id_miembro, $_POST['nuevo']);
        $mensaje = 'Se ha insertado una nueva referencia. ';
    }


    // Recorre los campos del formulario
    for ($i = 0; $i < $_POST['numero']; ++$i) {

        // Si esta marcado para borrar o está vacío el campo
        if ($_POST['borrar_'.$i] || empty($_POST['referencia_'.$i])) {
            deleteBibtex($_POST['id_'.$i], $id_miembro);
            $delete++;

        } else {
            // En caso contrario, lo actualiza
            updateBibtex($_POST['id_'.$i], $id_miembro,
                $_POST['referencia_'.$i]);
            $update++;
        }
    }


    // Crea el mensaje conlos cambios
    if (!empty($update)) {
        $mensaje .= 'Se han actualizado ' . $update . ' referencia/s. ';
    }
    if (!empty($delete)) {
        $mensaje .= 'Se han borrado ' . $delete . ' referencia/s.';
    }

}


// Obtiene todas las referencias del miembro
$bibtex = getMiembroBibtex($id_miembro);


// Incluye la vista de la pagina
require_once 'vista/' . $_file . '.php';


?>
