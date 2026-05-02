<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "veterinaria");

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a BD"]));
}

$data = json_decode(file_get_contents("php://input"), true);

$token = $data["token"] ?? "";
$password = $data["password"] ?? "";

if (!$token || !$password) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit();
}

// Encriptar contraseña
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Validar token
$sql = "SELECT * FROM usuarios 
        WHERE reset_token=? 
        AND token_expira > NOW()";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    // Actualizar contraseña
    $update = $conn->prepare("
        UPDATE usuarios 
        SET password=?, 
            reset_token=NULL, 
            token_expira=NULL 
        WHERE reset_token=?
    ");

    $update->bind_param("ss", $passwordHash, $token);
    $update->execute();

    echo json_encode(["success" => true]);

} else {
    echo json_encode(["error" => "Token inválido o expirado"]);
}
?>