<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data["nombre"];
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_BCRYPT);
$rol = $data["rol"];

// 🔍 VALIDAR SI EL EMAIL YA EXISTE
$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["error" => "El correo ya está registrado"]);
    exit();
}

// ✅ CREAR USUARIO
$sql = "INSERT INTO usuarios (nombre, email, password, rol, activo, created_at) 
        VALUES (?, ?, ?, ?, 1, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $password, $rol);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error al crear usuario"]);
}
?>