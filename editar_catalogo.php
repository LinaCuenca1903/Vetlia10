<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");

$id = $_POST["id"];
$nombre = $_POST["nombre"];
$tipo = $_POST["tipo"];
$precio = $_POST["precio"];
$stock = $_POST["stock"];
$descripcion = $_POST["descripcion"];

$sql = "UPDATE catalogo
SET
nombre=?,
tipo=?,
precio=?,
stock=?,
descripcion=?
WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdisi",
    $nombre,
    $tipo,
    $precio,
    $stock,
    $descripcion,
    $id
);

$stmt->execute();

echo json_encode([
    "success" => true
]);

?>