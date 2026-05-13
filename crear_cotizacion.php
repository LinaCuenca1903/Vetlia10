<?php
// crear_cotizacion.php

header("Content-Type: application/json");

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "veterinaria"
);

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$mascota_id = $data["mascota_id"];
$usuario_id = $data["usuario_id"];
$total = $data["total"];
$items = $data["items"];
$estado = $data["estado"];

$sql = "INSERT INTO cotizaciones
(mascota_id,usuario_id,fecha,total,estado)
VALUES
(?,?,NOW(),?,?)

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iids",
    $mascota_id,
    $usuario_id,
    $total
);

$stmt->execute();

$cotizacion_id = $conn->insert_id;

foreach($items as $item){

    $catalogo_id = $item["catalogo_id"];
    $cantidad = $item["cantidad"];
    $precio = $item["precio"];
    $subtotal = $item["subtotal"];

    $sqlDetalle = "
    INSERT INTO detalle_cotizacion
    (cotizacion_id,catalogo_id,cantidad,precio,subtotal)
    VALUES
    (?,?,?,?,?)
    ";

    $stmtDetalle =
    $conn->prepare($sqlDetalle);

    $stmtDetalle->bind_param(
        "iiidd",
        $cotizacion_id,
        $catalogo_id,
        $cantidad,
        $precio,
        $subtotal
    );

    $stmtDetalle->execute();
}

echo json_encode([
    "success" => true
]);
?>