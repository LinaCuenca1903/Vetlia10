<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = json_decode(file_get_contents("php://input"), true);

$mascota_id = $data["mascota_id"];
$usuario_id = $data["usuario_id"];
$total      = $data["total"];
$estado     = $data["estado"];
$items      = $data["items"];

$sql = "INSERT INTO cotizaciones
(mascota_id,usuario_id,fecha,total,estado)
VALUES
(?,?,NOW(),?,?)";

$stmt = $conn->prepare($sql);

if(!$stmt){
    die(json_encode([
        "success"=>false,
        "error"=>$conn->error
    ]));
}

$stmt->bind_param(
    "iids",
    $mascota_id,
    $usuario_id,
    $total,
    $estado
);

if(!$stmt->execute()){
    die(json_encode([
        "success"=>false,
        "error"=>$stmt->error
    ]));
}

$cotizacion_id = $conn->insert_id;

foreach($items as $item){

    $sqlDetalle = "INSERT INTO detalle_cotizacion
    (cotizacion_id,catalogo_id,cantidad,precio,subtotal)
    VALUES (?,?,?,?,?)";

    $stmtDetalle = $conn->prepare($sqlDetalle);

    $stmtDetalle->bind_param(
        "iiidd",
        $cotizacion_id,
        $item["catalogo_id"],
        $item["cantidad"],
        $item["precio"],
        $item["subtotal"]
    );

    $stmtDetalle->execute();
}

echo json_encode([
    "success"=>true
]);