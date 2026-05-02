<?php
include("db.php");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !isset($data["password"])) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$email = $conn->real_escape_string($data["email"]);
$password = $data["password"];

$sql = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email='$email' AND activo=1";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
        
        // ⚠️ Nunca enviar password al frontend
        unset($user["password"]);

        echo json_encode([
            "success" => true,
            "message" => "Login exitoso",
            "user" => $user
        ]);
    } else {
        echo json_encode(["error" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["error" => "Usuario no encontrado"]);
}
?>