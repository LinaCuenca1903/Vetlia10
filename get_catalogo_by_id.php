<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$id = intval($_GET["id"]);

$sql = "SELECT * FROM catalogo WHERE id = $id";

$r = $conn->query($sql);

echo json_encode(
    $r->fetch_assoc()
);

?>