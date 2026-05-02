<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data["nombre"];
$telefono = $data["telefono"];
$direccion = $data["direccion"];
$email = $data["email"];

$sql = "INSERT INTO tutores (nombre, telefono, direccion, email, created_at)
        VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $telefono, $direccion, $email);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "id" => $stmt->insert_id]);
} else {
    echo json_encode(["error" => "Error al crear tutor"]);
}
?>