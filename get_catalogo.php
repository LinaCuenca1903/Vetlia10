<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$data = [];

$sql = "SELECT * FROM catalogo ORDER BY id DESC";

$r = $conn->query($sql);

while($row = $r->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);

?>