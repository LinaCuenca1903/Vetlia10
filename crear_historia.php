<?php
header("Content-Type: application/json");

ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{

$conn = new mysqli("localhost","root","","veterinaria");
$conn->set_charset("utf8");

$data = json_decode(file_get_contents("php://input"), true);

if(empty($data['mascota_id'])){
throw new Exception("Paciente requerido");
}

$mascota = intval($data['mascota_id']);
$usuario = intval($data['usuario_id']);

$motivo = $conn->real_escape_string($data['motivo']);
$peso = floatval($data['peso']);
$temp = floatval($data['temperatura']);
$diag = $conn->real_escape_string($data['diagnostico']);
$trat = $conn->real_escape_string($data['tratamiento']);
$obs = $conn->real_escape_string($data['observaciones']);
$control = $data['proximo_control'];

$sql = "INSERT INTO historias_clinicas
(mascota_id, usuario_id, motivo, peso, temperatura, diagnostico, tratamiento, observaciones, proximo_control, fecha)
VALUES ($mascota,$usuario,'$motivo',$peso,$temp,'$diag','$trat','$obs','$control',NOW())";

$conn->query($sql);

echo json_encode(["success"=>true]);

}catch(Throwable $e){

echo json_encode([
"success"=>false,
"error"=>$e->getMessage()
]);

}