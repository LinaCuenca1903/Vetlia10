<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$id = intval($_GET["id"]);

$sql = "DELETE FROM catalogo WHERE id = $id";

$conn->query($sql);

echo json_encode([
    "success" => true
]);

?>