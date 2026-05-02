<?php
header("Content-Type: application/json");

ini_set('display_errors', 0);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{

    $conn = new mysqli("localhost","root","","veterinaria");
    $conn->set_charset("utf8");

    $data = json_decode(file_get_contents("php://input"), true);

    if(
        empty($data['id']) ||
        empty($data['diagnostico']) ||
        empty($data['tratamiento'])
    ){
        echo json_encode(["success"=>false,"error"=>"Datos incompletos"]);
        exit;
    }

    $id = intval($data['id']);
    $diagnostico = $conn->real_escape_string($data['diagnostico']);
    $tratamiento = $conn->real_escape_string($data['tratamiento']);

    $sql = "
    UPDATE historias_clinicas
    SET diagnostico='$diagnostico', tratamiento='$tratamiento'
    WHERE id=$id
    ";

    if(!$conn->query($sql)){
        echo json_encode(["success"=>false,"error"=>$conn->error]);
        exit;
    }

    echo json_encode(["success"=>true]);

}catch(Throwable $e){
    echo json_encode([
        "success"=>false,
        "error"=>$e->getMessage()
    ]);
}