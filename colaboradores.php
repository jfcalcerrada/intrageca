<?php
// Inicializamos el archivo con el script
include("common/init.php");
include("autenticacion.php");
// Autenticamos al usuario
autenticar_usuario();

//--------------------------------------------------------------------------
// colaboradores.php
//
// Genera la página principal de colaboradores y los exporta al template
// colaboradores.html. Se genera una lista de grupos de colaboracion, cada
// uno de los cuales contiene una serie de miembros y proyectos en los
// que colabora.
//--------------------------------------------------------------------------

// Declara arrays a usar
$lista_id_miembros = array();

//--------------------------------------------------------------------
// CONSULTA DE COLABORADORES
//--------------------------------------------------------------------
// inicia el valor del ultimo_grupo
$ultimo_grupo = 1;

// Definicion de consulta a la base de datos
$consulta_grupos =
    "SELECT id_grupo, nombre_grupo, descripcion, link_grupo, publico ".
    "FROM grupos_colaboradores ".
    "ORDER BY publico DESC, nombre_grupo ASC";

// Realiza consulta y verifica si es correcta
if (!($resultado_grupos = mysql_query($consulta_grupos)))
    ERR_muestra_pagina_error("Error en consulta: $consulta_grupos");

// Imprime cada grupo
while ($grupos = mysql_fetch_array($resultado_grupos)) {

    // Verifica si no es publico
    if (($ultimo_grupo != $grupos['publico']) && ($grupos['publico'] == 0)) {
        // Cierra la lista anterior
        $contenido->parse("content.grupo_colaboradores.grupo");

        // Inicia una nueva lista con desactivos
        $contenido->parse("content.grupo_colaboradores.cab_desactiva");
        
        // Resetea flag para no volver a entrar
        $ultimo_grupo = 0;
    }
    
    // Asigna los valores de cada grupo
    $lista_valores = array (
        'GRUPO_COLABORADOR' => $grupos['nombre_grupo'],
        'DESCRIPCION' => $grupos['descripcion'],
        'LINK_GRUPO' => $grupos['link_grupo']);

    $contenido->assign("IDC", $grupos['id_grupo']);
    $contenido->parse("content.grupo_colaboradores.cabecera.editar");

    // Estilo, para mostrar bien el boton sin tablas
    $contenido->assign("STYLE", 'style="float: left;"');

    // Los imprime en página
    $contenido->assign("LISTA", $lista_valores);
    $contenido->parse("content.grupo_colaboradores.cabecera");

    //-------------------------------------
    // Obtiene todos los miembros del grupo
    //-------------------------------------
    $consulta_colaboradores =
        "SELECT nombre, puesto, email_colaborador, ".
        "link_colaborador, id_colaborador, director ".
        "FROM colaboradores ".
        "WHERE grupo_pertenece = '{$grupos['id_grupo']}'";

    // Realiza consulta para ver campos distintos y verifica que se ejecuto bien
    if (!($resultado_colaboradores = mysql_query($consulta_colaboradores)))
        ERR_muestra_pagina_error("Error en consulta: $consulta_colaboradores");

    // Inicializa lista de id miembros
    $lista_id_miembros = array();
    $directores = 0;

    // Imprime para cada miembro del grupo
    while ($miembro = mysql_fetch_array($resultado_colaboradores)) {
        // Almacena los ID's de los miembros para proyectos
        $lista_id_miembros[$miembro['id_colaborador']] = 1;

        // Si es director, lo muestra
        if ($miembro['director'] == 1) {
            // Selecciona por defecto el e-mail
            $link_persona = "mailto:{$miembro['email_colaborador']}";

            // Si hay link, cambia el e-mail por el link
            if (strlen($miembro['link_colaborador']) > 0)
            $link_persona = $miembro['link_colaborador'];

            // Asigna valores de grupo
            $lista_valores = array (
                'NOMBRE' => $miembro['nombre'],
                'PUESTO' => $miembro['puesto'],
                'LINK_PERSONA' => $link_persona);

            // Los imprime en la página
            $contenido->assign("LISTA", $lista_valores);
            $contenido->parse("content.grupo_colaboradores.colaboradores.fila");

            $directores++;
        }
    }

    // Imprime cabecera de colaboradores si hay algún miembro
    //if (mysql_num_rows($resultado_colaboradores) > 0)
    if($directores > 0)
        $contenido->parse("content.grupo_colaboradores.colaboradores");

    //-------------------------------------------------
    // Obtiene todos los proyectos en los que colaboran
    //-------------------------------------------------
    // Construye consulta de colaboracion con los miembros del grupo
    $consulta_proy =
        "SELECT titulo, descrip_corta, proyecto_idiomas.id_proyecto ".
        "FROM colaborador_proyectos LEFT JOIN proyecto_idiomas ".
        "ON colaborador_proyectos.id_proyecto = proyecto_idiomas.id_proyecto ".
        "WHERE idioma = '$idioma' AND id_colaborador in (0";

    // Añadimos los miembros del grupo a la busqueda para obtener los proyectos
    foreach ($lista_id_miembros as $id_col => $dummy)
        $consulta_proy .= ", $id_col";

    $consulta_proy .= ")";

    // Verifica que se ejecuto bien
    if (!($resultado_proy = mysql_query($consulta_proy)))
        ERR_muestra_pagina_error("Error en consulta: $consulta_proy");

    // Imprime para cada proyecto
    while ($proyecto = mysql_fetch_array($resultado_proy)) {
        // Asigna valores de grupo
        $lista_valores = array (
            'TITULO' => $proyecto['titulo'],
            'DESC' => $proyecto['descrip_corta'],
            'LINK_PROY' => $proyecto['proyecto_idiomas']);

        // Imprimelos en página
        $contenido->assign("LISTA", $lista_valores);
        $contenido->parse("content.grupo_colaboradores.proyectos.fila");
    }

    // Imprime cabecera de proyectos si hay alguno
    if (mysql_num_rows($resultado_proy) > 0)
        $contenido->parse("content.grupo_colaboradores.proyectos");

    // Cierra el grupo
    $contenido->parse("content.grupo_colaboradores");
}

// Mostramos el boton de añadir grupo a administrador
if($_SESSION['id_usuario'] == 0)
    $contenido->parse("content.anyadir");


// Cierra la conexion con mysql
mysql_close($conexion);

// Parsea el contenido
$contenido->parse("content");

// Muestra la pagina final
mostrar_pagina($archivo, $contenido);

?>