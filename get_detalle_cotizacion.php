<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$id = $_GET['id'];

$response = [];

/* OBTENER COTIZACION */

$sqlCot = "
SELECT *
FROM cotizaciones
WHERE id = $id
";

$rCot = $conn->query($sqlCot);

$response = $rCot->fetch_assoc();

/* OBTENER ITEMS */

$response["items"] = [];

$sql = "
SELECT dc.*,
c.nombre
FROM detalle_cotizacion dc
INNER JOIN catalogo c
ON dc.catalogo_id = c.id
WHERE dc.cotizacion_id = $id
";

$r = $conn->query($sql);

while($row = $r->fetch_assoc()){

    $response["items"][] = $row;

}

echo json_encode($response);

?>