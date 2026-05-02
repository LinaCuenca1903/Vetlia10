<?php
header("Content-Type: application/json");

// 🔥 MOSTRAR ERRORES COMO JSON (NO HTML)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    // 🔥 CONEXIÓN DIRECTA (ROOT)
    $conn = new mysqli("localhost", "root", "", "veterinaria");
    $conn->set_charset("utf8");

    // 🔥 LEER JSON
    $data = json_decode(file_get_contents("php://input"), true);

    if(!$data){
        echo json_encode([
            "success"=>false,
            "error"=>"No llegó JSON"
        ]);
        exit;
    }

    // 🔥 VALIDAR DATOS
    if(
        empty($data['mascota_id']) ||
        empty($data['usuario_id']) ||
        empty($data['fecha']) ||
        empty($data['hora'])
    ){
        echo json_encode([
            "success"=>false,
            "error"=>"Datos incompletos"
        ]);
        exit;
    }

    $mascota_id = intval($data['mascota_id']);
    $usuario_id = intval($data['usuario_id']);
    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $motivo = $conn->real_escape_string($data['motivo'] ?? "");

    // ================= VALIDAR HORARIO =================
    $check = $conn->query("
        SELECT id FROM citas 
        WHERE fecha='$fecha' 
        AND hora='$hora' 
        AND estado!='Cancelada'
    ");

    if($check->num_rows > 0){
        echo json_encode([
            "success"=>false,
            "error"=>"Horario ocupado"
        ]);
        exit;
    }

    // ================= INSERT =================
    $sql = "INSERT INTO citas 
    (mascota_id, usuario_id, fecha, hora, estado, motivo)
    VALUES ($mascota_id,$usuario_id,'$fecha','$hora','Pendiente','$motivo')";

    $conn->query($sql);

    // ================= RESPUESTA =================
    echo json_encode([
        "success"=>true,
        "message"=>"Cita creada correctamente"
    ]);

} catch (Exception $e) {

    // 🔥 IMPORTANTE: devolver JSON, NO HTML
    echo json_encode([
        "success"=>false,
        "error"=>$e->getMessage()
    ]);
}