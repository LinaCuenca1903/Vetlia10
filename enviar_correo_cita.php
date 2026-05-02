<?php
header("Content-Type: application/json");

// 🔥 OCULTAR ERRORES HTML
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 🔥 CAPTURAR ERRORES MYSQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{

    // ================= CONEXIÓN =================
    $conn = new mysqli("localhost","root","","veterinaria");
    $conn->set_charset("utf8");

    // ================= RECIBIR DATA =================
    $data = json_decode(file_get_contents("php://input"), true);

    if(empty($data['id'])){
        echo json_encode(["success"=>false,"error"=>"ID requerido"]);
        exit;
    }

    $id = intval($data['id']);

    // ================= OBTENER DATOS =================
    $res = $conn->query("
        SELECT 
        c.fecha, c.hora, c.motivo,
        m.nombre AS mascota,
        t.nombre AS tutor,
        t.email
        FROM citas c
        JOIN mascotas m ON c.mascota_id = m.id
        JOIN tutores t ON m.tutor_id = t.id
        WHERE c.id = $id
    ");

    if($res->num_rows == 0){
        echo json_encode(["success"=>false,"error"=>"Cita no encontrada"]);
        exit;
    }

    $cita = $res->fetch_assoc();

    if(empty($cita['email'])){
        echo json_encode(["success"=>false,"error"=>"Tutor sin correo"]);
        exit;
    }

    // ================= PHPMailer =================
    require __DIR__ . '/vendor/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/vendor/PHPMailer/src/SMTP.php';
    require __DIR__ . '/vendor/PHPMailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // ⚠️ DEBUG (puedes poner 0 después)
    $mail->SMTPDebug = 0;

    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "default@gmail.com";
    $mail->Password = "cdefault"; // tu app password
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    // ================= CONFIG CORREO =================
    $mail->setFrom("soporteclientmerge@gmail.com","Vetlia 🐾");
    $mail->addAddress($cita['email'], $cita['tutor']);

    $mail->isHTML(true);
    $mail->Subject = "Cita veterinaria 🐾";

    // 🔥 AQUÍ ESTABA TU ERROR
    $mail->Body = "
    <h2>Hola {$cita['tutor']} 👋</h2>
    <p>Te recordamos la cita de tu mascota <b>{$cita['mascota']}</b></p>
    <p><b>Fecha:</b> {$cita['fecha']}</p>
    <p><b>Hora:</b> {$cita['hora']}</p>
    <p><b>Motivo:</b> {$cita['motivo']}</p>
    <br>
    <p>Gracias por confiar en Vetlia 🐾</p>
    ";

    // ================= ENVIAR =================
    $mail->send();

    echo json_encode([
        "success"=>true,
        "message"=>"Correo enviado correctamente"
    ]);

}catch(Throwable $e){

    echo json_encode([
        "success"=>false,
        "error"=>$e->getMessage()
    ]);
}