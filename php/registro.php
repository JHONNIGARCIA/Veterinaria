<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../connection/db_conexion.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
  if ($method === 'GET') {
    $limite = (int)($_GET['limite'] ?? 200);
    $limite = max(1, min(500, $limite));
    $q = $pdo->prepare(
      "SELECT id_cita, raza_mascota, nombre_mascota, doctor, motivo, fecha_creacion
       FROM citas
       ORDER BY id_cita DESC
       LIMIT ?"
    );
    $q->bindValue(1, $limite, PDO::PARAM_INT);
    $q->execute();
    echo json_encode(['ok'=>true, 'citas'=>$q->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE);
    exit;
  }

  if ($method === 'POST') {
    $raza   = trim($_POST['raza_mascota']   ?? $_POST['raza']   ?? '');
    $nombre = trim($_POST['nombre_mascota'] ?? $_POST['nombre'] ?? '');
    $doctor = trim($_POST['doctor'] ?? '');
    $motivo = trim($_POST['motivo'] ?? '');
    if ($raza==='' || $nombre==='' || $doctor==='' || $motivo==='') {
      http_response_code(400);
      echo json_encode(['ok'=>false,'msg'=>'Faltan campos: raza, nombre, doctor y motivo.']); exit;
    }
    $stmt = $pdo->prepare(
      "INSERT INTO citas (raza_mascota, nombre_mascota, doctor, motivo)
       VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$raza, $nombre, $doctor, $motivo]);
    echo json_encode(['ok'=>true,'msg'=>'Cita guardada','id'=>$pdo->lastInsertId()]);
    exit;
  }

  http_response_code(405);
  echo json_encode(['ok'=>false,'msg'=>'Método no permitido']);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'msg'=>'Error del servidor','error'=>$e->getMessage()]);
}
