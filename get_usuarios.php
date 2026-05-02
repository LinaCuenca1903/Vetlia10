<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

$sql = "SELECT id, nombre, email, rol, activo FROM usuarios";
$result = $conn->query($sql);

$usuarios = [];

while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);
?>