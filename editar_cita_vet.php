<?php

// SIEMPRE lo primero — antes de cualquier cosa
header("Content-Type: application/json");

// Apagar el HTML de errores de PHP (para que no rompa el JSON)
ini_set("display_errors", 0);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

  $conn = new mysqli("localhost", "root", "", "veterinaria");
  $conn->set_charset("utf8");

  $data = json_decode(file_get_contents("php://input"), true);

  // Validar que llegaron los datos
  if (!$data) {
    echo json_encode(["success" => false, "error" => "No se recibieron datos"]);
    exit;
  }

  $id         = intval($data["id"]         ?? 0);
  $mascota_id = intval($data["mascota_id"] ?? 0);
  $fecha      = $data["fecha"]  ?? "";
  $hora       = $data["hora"]   ?? "";
  $motivo     = $conn->real_escape_string($data["motivo"] ?? "");

  if (!$id || !$mascota_id || !$fecha || !$hora) {
    echo json_encode([
      "success" => false,
      "error"   => "Datos incompletos",
      "recibido" => $data  // para ver qué llegó exactamente
    ]);
    exit;
  }

  // Evitar choque de horarios
  $check = $conn->query("
    SELECT id FROM citas
    WHERE fecha = '$fecha'
    AND hora = '$hora'
    AND estado != 'Cancelada'
    AND id != $id
  ");

  if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Horario ocupado"]);
    exit;
  }

  $conn->query("
    UPDATE citas
    SET
      mascota_id = $mascota_id,
      fecha      = '$fecha',
      hora       = '$hora',
      motivo     = '$motivo'
    WHERE id = $id
  ");

  // Verificar que realmente actualizó algo
  if ($conn->affected_rows === 0) {
    echo json_encode([
      "success" => false,
      "error"   => "No se actualizó ningún registro — id=$id puede no existir"
    ]);
    exit;
  }

  echo json_encode(["success" => true]);

} catch (Exception $e) {
  echo json_encode([
    "success" => false,
    "error"   => $e->getMessage()
  ]);
}