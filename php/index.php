<?php
// php/index.php  -> endpoint para registrar por AJAX
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'message'=>'Solo se admite POST']);
  exit;
}

// Conexión PDO
require_once __DIR__ . '/../connection/db_conexion.php';

try {
  // Usar la conexión global que se crea en db_conexion.php
  if (!isset($pdo)) {
    global $pdo;
  }
  if (!($pdo instanceof PDO)) {
    throw new Exception('No se pudo establecer conexión con la base de datos');
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'message'=>'Error de conexión: ' . $e->getMessage()]);
  exit;
}

// Datos del formulario
$raza   = trim($_POST['raza']   ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$doctor = trim($_POST['doctor'] ?? '');
$motivo = trim($_POST['motivo'] ?? '');

// Validaciones
if ($raza==='' || $nombre==='' || $doctor==='' || $motivo==='') {
  http_response_code(422);
  echo json_encode(['ok'=>false,'message'=>'Todos los campos son obligatorios']);
  exit;
}
if (mb_strlen($raza)>80 || mb_strlen($nombre)>100 || mb_strlen($doctor)>100) {
  http_response_code(422);
  echo json_encode(['ok'=>false,'message'=>'Longitud de texto excedida']);
  exit;
}

// Insert en db_mascotas.citas
try {
  $stmt = $pdo->prepare(
    'INSERT INTO citas (raza_mascota, nombre_mascota, doctor, motivo, fecha_creacion) VALUES (?,?,?,?,NOW())'
  );
  $stmt->execute([$raza, $nombre, $doctor, $motivo]);
  echo json_encode(['ok'=>true,'message'=>'¡Cita registrada exitosamente para ' . $nombre . '!']);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'message'=>'Error al guardar la cita','error'=>$e->getMessage()]);
}
