<?php
/**
 * Backend para consultar las citas registradas
 * Devuelve las citas en formato JSON
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Solo permitir GET para consultar
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Solo se permite GET para consultar citas']);
    exit;
}

// Incluir la conexión a la base de datos
require_once __DIR__ . '/../connection/db_conexion.php';

try {
    // Usar la conexión global
    global $pdo;
    
    if (!($pdo instanceof PDO)) {
        throw new Exception('No se pudo establecer conexión con la base de datos');
    }
    
    // Consultar todas las citas ordenadas por fecha más reciente
    $sql = "SELECT 
                id_cita, 
                raza_mascota, 
                nombre_mascota, 
                doctor, 
                motivo, 
                fecha_creacion 
            FROM citas 
            ORDER BY fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verificar si hay citas
    if (empty($citas)) {
        echo json_encode([
            'ok' => true,
            'message' => 'No hay citas registradas',
            'citas' => [],
            'total' => 0
        ]);
    } else {
        echo json_encode([
            'ok' => true,
            'message' => 'Citas cargadas exitosamente',
            'citas' => $citas,
            'total' => count($citas)
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error en la base de datos',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error del servidor',
        'error' => $e->getMessage()
    ]);
}
?>
