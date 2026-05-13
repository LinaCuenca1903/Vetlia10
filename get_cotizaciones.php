<?php

header("Content-Type: application/json");

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "veterinaria"
);

$mascota_id = intval($_GET["mascota_id"]);

$sql = "SELECT *
FROM cotizaciones
WHERE mascota_id = $mascota_id
ORDER BY id DESC";

$r = $conn->query($sql);

$data = [];

while($row = $r->fetch_assoc()){

    $data[] = $row;
}

echo json_encode($data);

?>