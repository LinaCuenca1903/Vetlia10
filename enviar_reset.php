<?php
require "config.php";
require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli("localhost", "root", "", "veterinaria");

if ($conn->connect_error) {
    die("Error de conexión.");
}

$correo = $_POST['correo'];

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Correo no encontrado.");
}

$token = bin2hex(random_bytes(32));
$expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

$update = $conn->prepare("UPDATE usuarios SET reset_token=?, token_expira=? WHERE email=?");
$update->bind_param("sss", $token, $expira, $correo);
$update->execute();

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'default@gmail.com';
    $mail->Password   = 'default;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('soporteclientmerge@gmail.com', 'Vetlia 🐾');
    $mail->addAddress($correo);

    //$link = "http://localhost/vetlia/reset.html?token=" . $token;
    $link = $base_url . "/reset.html?token=" . $token;

    $mail->isHTML(true);
    $mail->Subject = 'Restablecer contraseña';
    $mail->Body    = "
        <h2>Vetlia 🐾</h2>
        <p>Haz clic para cambiar tu contraseña:</p>
        <a href='$link'>$link</a>
        <p>Este enlace expira en 1 hora.</p>
    ";

    $mail->send();
    echo "Correo enviado correctamente.";
} catch (Exception $e) {
    echo "Error al enviar: {$mail->ErrorInfo}";
}
?>