<?php

header("Content-Type: application/json");

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data["id"];
$correo = $data["correo"];

// PDF URL
$pdfUrl =
"http://localhost/vetlia/generar_pdf.php?id="
.$id;

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'TU_CORREO@gmail.com';

    $mail->Password = 'TU_PASSWORD_APP';

    $mail->SMTPSecure = 'tls';

    $mail->Port = 587;

    $mail->setFrom(
        'TU_CORREO@gmail.com',
        'Vetlia'
    );

    $mail->addAddress($correo);

    $mail->Subject =
    'Cotizacion Veterinaria';

    $mail->Body =
    'Adjuntamos su cotizacion veterinaria.';

    $mail->addStringAttachment(
        file_get_contents($pdfUrl),
        "cotizacion.pdf"
    );

    $mail->send();

    echo json_encode([
        "success" => true
    ]);

}catch(Exception $e){

    echo json_encode([
        "success" => false,
        "error" => $mail->ErrorInfo
    ]);
}
?>