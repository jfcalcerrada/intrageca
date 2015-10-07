<?

//--------------------------------------------------------------------------
// miembro_insertar.php
//
// Inserta o actualiza los valores de un miembro enviados a traves de
// formulario en la base de datos. Los parámetros se añaden via POST.
//
// Parametros de entrada
//   id_miembro : el número de identidad del miembro
//   idioma : el idioma de los datos que se actualizan
//   nombre : El nombre del miembro
//   categoria : la categoría del miembro
//   puesto : el puesto del miembro
//   afiliacion : Afiliación del miembro
//   activo : Indica si el usuario está activo
//  datos de contacto:
//   direccion, telefono, fax, email
//  datos de curriculum:
//   curriculum
// 
// La foto y el curriculum se envían mediante POST.
// - La foto se copiará al directorio fotos/
// - El curriculum se copiará al directorio docs/
//   
//--------------------------------------------------------------------------
/**
 *
 * @global <type> $mbr_dir_fotos
 * @global <type> $mime_fotos
 * @global <type> $mbr_dir_docs
 * @global <type> $mime_cv
 * @global <type> $gen_idiomas_disp
 * @global <type> $errors
 * @global <type> $HTTP_POST_FILES
 * @param <type> $registro
 * @return <type>
 */

function miembro_insertar($registro)
{

// definicion de common
  GLOBAL $mbr_dir_fotos;
  GLOBAL $mime_fotos;
  GLOBAL $mbr_dir_docs;
  GLOBAL $mime_cv;

  GLOBAL $gen_idiomas_disp;
  GLOBAL $errors;

  GLOBAL $HTTP_POST_FILES;


    /*
     * COMPRUEBA SI EXISTE LA FOTO Y LA COPIA EN LOCAL
     */
  // Comprueba si esta definida
  if (isset($HTTP_POST_FILES['foto']['name']) && strlen($HTTP_POST_FILES['foto']['name']) > 0) {

  // Comprobamos que sea de uno de los tipos permitidos
    if (strpos($mime_fotos, $HTTP_POST_FILES['foto']['type']) !== false) {
    // Extraemos la extension del archivo
      $ext_foto =
          strtolower(substr(strrchr($HTTP_POST_FILES['foto']['name'], '.'), 1));

      // Copiamos el fichero renombrandolo
      $archivo_foto =
          "{$mbr_dir_fotos}f{$registro['id_miembro']}.$ext_foto";
      copy($HTTP_POST_FILES['foto']['tmp_name'], $archivo_foto);

    } else {

    }
  }

    /*
     * COMPRUEBA SI EXISTE EL CURRICULUM Y LO COPIA EN LOCAL
     */
  // Comprueba si esta definida
  if (isset($HTTP_POST_FILES['cv']['name']) && strlen($HTTP_POST_FILES['cv']['name']) > 0) {

  // Comprueba si es de los tipos permitidos
    if (strpos($mime_cv, $HTTP_POST_FILES['cv']['type']) !== false) {
    // Extraemos la extension del archivo
      $ext_cv =
          strtolower(substr(strrchr($HTTP_POST_FILES['cv']['name'], '.'), 1));

      // Copia el fichero en local renombrandolo
      $archivo_cv = "{$mbr_dir_docs}c{$registro['id_miembro']}_".
          "{$registro['idioma']}.$ext_cv";
      copy($HTTP_POST_FILES['cv']['tmp_name'], $archivo_cv);

    } else {

    }
  }

  //--------------------------------------------------------------------
  // ACTUALIZA DATOS EN TABLA MIEMBROS
  //--------------------------------------------------------------------

  // Carga el idioma
  $idioma = $registro['idioma'];


  // Prepara los datos para insertar en la base de datos
  $nombre     = addslashes($registro['nombre']);
  $apellidos  = addslashes($registro['apellidos']);
  $categoria  = addslashes($registro['categoria']);
  $puesto     = addslashes($registro['puesto']);
  $afiliacion = addslashes($registro['afiliacion']);
  $direccion  = addslashes($registro['direccion']);
  $telefono   = addslashes($registro['telefono']);
  $fax        = addslashes($registro['fax']);
  $email      = addslashes($registro['email']);
  $curriculum = addslashes($registro['curriculum']);


  if (isset($registro['dia_inc']) && isset($registro['dia_inc'])
      && isset($registro['dia_inc'])
      && checkdate($registro['mes_inc'], $registro['dia_inc'], $registro['anyo_inc']) ) {

    $fecha_incorporacion = "{$registro['anyo_inc']}-{$registro['mes_inc']}-".
        "{$registro['dia_inc']}";
  } else {
    $fecha_incorporacion = $registro['fecha_incorporacion'];
  }

  // Comprobamos si esta activo o no
  $activo     = ($registro['activo'] == 1) ? '1' : '0';


  // Si es un nuevo miembro crear la inserccion, por defecto NO ACTIVO
  if ($registro['id_miembro'] == 0) {
    $consulta_miembro =
        "INSERT INTO miembros(nombre, apellidos, categoria, activo, ".
        "direccion, telefono, fax, email, fecha_incorporacion) ".
        "VALUES ('$nombre', '$apellidos', '$categoria', '0', '$direccion', ".
        "'$telefono', '$fax', '$email', '$fecha_incorporacion')";

  // Si ya existe el miembro actualizamos los datos
  } else {
    $consulta_miembro =
        "UPDATE miembros ".
        "SET nombre = '$nombre', apellidos = '$apellidos', ".
        "categoria = '$categoria', activo = '$activo', ".
        "direccion = '$direccion', telefono = '$telefono', ".
        "fax = '$fax', email = '$email', ".
        "fecha_incorporacion = '$fecha_incorporacion' ".
        "WHERE id_miembro = {$registro['id_miembro']}";
  }

  // Realizamos la consulta y comprobamos si da error
  mysql_query($consulta_miembro)
      or error($errors['actualiza'], "Error en la consulta: $consulta_miembro");

  // Realiza tareas de inicializacion de nuevo usuario
  if ($registro['id_miembro'] == 0) {
  // Obtiene ID del nuevo miembro
    $id_miembro = mysql_insert_id();

    // Si se ha introducido usuario
    if (isset($_POST['usuario']) && strlen($_POST['usuario']) >= 4) {
      $usuario = $_POST['usuario'];

      // Comprueba que no exista
      $consulta_usuario =
          "SELECT id_miembro ".
          "FROM miembro_autentica ".
          "WHERE usuario_web = '$usuario'";

      $resultado_usuario = mysql_query($consulta_usuario)
          or error($errors['consulta'],
          "Error en la consulta: $consulta_usuario");

      // Si existe algún miembro con ese usuario
      if (mysql_num_rows($resultado_usuario) != 0)
        $usuario = "defecto$id_miembro";

    // Si no se ha introducido usuario
    } else {
      $usuario = "defecto$id_miembro";
    }

        /* CREA LOS VALORES DE INICIO DEL MIEMBRO */
    $consulta_autenticacion =
      "INSERT INTO miembro_autentica(id_miembro, usuario_web, ".
        "password_web) ".
      "VALUES ('$id_miembro', '$usuario', MD5('".PASSWORD_DEFECTO."'))";

    // Realizamos la consulta y comprobamos si da error
    mysql_query($consulta_autenticacion)
        or error($errors['actualiza'],
        "Error en la consulta: $consulta_autenticacion");


  // Obtiene ID del miembro actualizado
  } else {
    $id_miembro = $registro['id_miembro'];
  }


  //---------------------------------------------
  // Actualiza valores dependientes de idioma
  //---------------------------------------------
  // verifica si registro ya está insertado
  $consulta_idiomas =
    "SELECT id_miembro ".
    "FROM miembro_idiomas ".
    "WHERE idioma = '$idioma' ".
      "AND id_miembro = '$id_miembro'";

  // ejecuta la consulta
  $resultado = mysql_query($consulta_idiomas)
    or error($errors['consulta'], "Error en la consulta: $consulta_idiomas");

  // Si no existe, inserta
  if (mysql_num_rows($resultado) == 0) {
    // inserta registro no insertado
    $consulta_idiomas =
      "INSERT INTO miembro_idiomas(id_miembro, idioma, puesto, ".
        "afiliacion, curriculum) ".
      "VALUES ('$id_miembro', '$idioma', '$puesto', ".
        "'$afiliacion', '$curriculum') ";

  // Si ya existe, actualiza
  } else {
    // actualiza tabla de idiomas
    $consulta_idiomas =
      "UPDATE miembro_idiomas ".
      "SET puesto = '$puesto', afiliacion = '$afiliacion', ".
        "curriculum = '$curriculum' ".
      "WHERE id_miembro = '$id_miembro' ".
        "AND idioma = '$idioma'";
  }

  // Realiza la consulta creada
  $resultado = mysql_query($consulta_idiomas)
    or error($errors['consulta'], "Error en la consulta: $consulta_idiomas");


  // Si se han introducido imagenes al insertar el nuevo miembro,
  // debemos renombrar el archivo correctamente
  if (strlen($archivo_foto) > 0) {
  // Creamos el nuevo nombre
    $link_foto = "{$mbr_dir_fotos}f{$id_miembro}.$ext_foto";
    // Renombramos
    rename($archivo_foto, $link_foto);

    // Añadimos la actualizacion de la foto
    $consulta =
        "UPDATE miembros ".
        "SET link_foto = '$link_foto' ".
        "WHERE id_miembro = $id_miembro; ";

    // Realizamos la consulta y comprobamos si da error
    mysql_query($consulta)
        or error($errors['actualiza'],
        "Error en la consulta: $consulta");
  }

  // Igual con el curriculum
  if (strlen($archivo_cv) > 0) {
  // Creamos el nuevo nombre
    $link_curriculum = "{$mbr_dir_docs}c{$id_miembro}_".
        "$idioma.$ext_cv";
    // Renombramos
    rename($archivo_cv, $link_curriculum);

    // Añadimos la inserccion del cv
    $consulta =
        "UPDATE miembro_idiomas ".
        "SET link_curriculum = '$link_curriculum' ".
        "WHERE id_miembro = $id_miembro ".
        "AND idioma = '$idioma'";

    // Realizamos la consulta y comprobamos si da error
    mysql_query($consulta)
        or error($errors['actualiza'],
        "Error en la consulta: $consulta");
  }


  return $id_miembro;
}

?>
