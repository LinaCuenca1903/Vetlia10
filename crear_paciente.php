<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

if ($conn->connect_error) {
    die(json_encode(["error" => "Error conexión BD"]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    die(json_encode(["error" => "No llegan datos"]));
}

$nombre = $data["nombre"];
$especie = $data["especie"];
$raza = $data["raza"];
$edad = $data["edad"];
$tutor_id = $data["tutor_id"];

$sql = "INSERT INTO mascotas (nombre, especie, raza, edad, tutor_id, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "Error en SQL"]));
}

$stmt->bind_param("sssii", $nombre, $especie, $raza, $edad, $tutor_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error al insertar"]);
}
?>