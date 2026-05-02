<?php
header("Content-Type: application/json");

// 🔥 NO mostrar errores en HTML
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 🔥 Convertir errores a excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{

    // 🔥 CONEXIÓN DIRECTA (sin include para evitar errores)
    $conn = new mysqli("localhost","root","","veterinaria");

    if($conn->connect_error){
        throw new Exception("Error conexión");
    }

    $conn->set_charset("utf8");

    // 🔥 LEER JSON
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if(!$data){
        throw new Exception("JSON inválido");
    }

    if(empty($data['id'])){
        throw new Exception("ID requerido");
    }

    $id = intval($data['id']);

    // 🔥 VALIDAR EXISTENCIA
    $check = $conn->query("SELECT id FROM historias_clinicas WHERE id=$id");

    if($check->num_rows == 0){
        throw new Exception("La historia no existe");
    }

    // 🔥 ELIMINAR
    $conn->query("DELETE FROM historias_clinicas WHERE id=$id");

    echo json_encode([
        "success"=>true
    ]);

}catch(Throwable $e){

    // 🔥 SIEMPRE JSON (NUNCA HTML)
    echo json_encode([
        "success"=>false,
        "error"=>$e->getMessage()
    ]);
}