<?php
include("db.php");

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data["nombre"];
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_BCRYPT);
$rol = $data["rol"];

$sql = "INSERT INTO usuarios (nombre, email, password, rol)
VALUES ('$nombre', '$email', '$password', '$rol')";

if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error"]);
}
?>