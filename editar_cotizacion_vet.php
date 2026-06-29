<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data["id"];
$estado = $data["estado"];
$total = $data["total"];
$items = $data["items"];

/* ACTUALIZAR COTIZACION */

$sql = "
UPDATE cotizaciones
SET
estado = '$estado',
total = '$total'
WHERE id = '$id'
";

$conn->query($sql);

/* ELIMINAR ITEMS ANTERIORES */

$conn->query("
DELETE FROM detalle_cotizacion
WHERE cotizacion_id = '$id'
");

/* INSERTAR NUEVOS ITEMS */

foreach($items as $i){

    $catalogo_id = $i["catalogo_id"];
    $cantidad = $i["cantidad"];
    $precio = $i["precio"];
    $subtotal = $i["subtotal"];

    $conn->query("

        INSERT INTO detalle_cotizacion(

            cotizacion_id,
            catalogo_id,
            cantidad,
            precio,
            subtotal

        ) VALUES(

            '$id',
            '$catalogo_id',
            '$cantidad',
            '$precio',
            '$subtotal'

        )

    ");

}

echo json_encode([
    "success" => true
]);

?>