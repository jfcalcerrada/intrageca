<?php

/**
 * Clase para la conexi�n con la base de datos
 */
class DB extends PDO
{
    private static $_instance = null;

    /**
     * Constructor de la clase DB, crea una conexion PDO con la base de datos
     * definida en �l
     *
     */
    public function __construct()
    {
        parent::__construct('mysql:host=localhost;dbname=webgeca_prod', 'webgrupo', 'webgrupo');
    }

    /**
     * M�todo que se usa para obtener una �nica instancia del objeto DB.
     * Si dicha instancia no existe, la crea. Una vez creada la devuelve.
     *
     * @return DB Instancia del objeto DB
     */
    public static function singleton()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
}
