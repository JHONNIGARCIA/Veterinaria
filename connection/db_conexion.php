<?php
/**
 * Archivo de conexión a la base de datos
 * Base de datos: db_mascotas
 * Sistema: Veterinaria
 */

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_mascotas";

try {
    // Crear conexión usando PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar para que devuelva arrays asociativos por defecto
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Mensaje de conexión exitosa (opcional, comentar en producción)
    // echo "Conexión exitosa a la base de datos db_mascotas";
    
} catch(PDOException $e) {
    // En caso de error en la conexión
    die("Error de conexión: " . $e->getMessage());
}

// También crear una conexión mysqli como alternativa (opcional)
$mysqli = new mysqli($servername, $username, $password, $database);

// Verificar conexión mysqli
if ($mysqli->connect_error) {
    die("Error de conexión mysqli: " . $mysqli->connect_error);
}

// Establecer charset para mysqli
$mysqli->set_charset("utf8");

/**
 * Función para cerrar las conexiones
 */
function cerrarConexiones() {
    global $pdo, $mysqli;
    $pdo = null;
    $mysqli->close();
}

/**
 * Función para obtener la conexión PDO
 */
function getConexionPDO() {
    global $pdo;
    return $pdo;
}

/**
 * Función para obtener la conexión MySQLi
 */
function getConexionMySQLi() {
    global $mysqli;
    return $mysqli;
}

?>
