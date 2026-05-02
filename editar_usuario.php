<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"];
$nombre = $data["nombre"];
$email = $data["email"];
$rol = $data["rol"];

$sql = "UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nombre, $email, $rol, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error al editar"]);
}
?>