<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

$sql = "SELECT 
          p.id, p.nombre, p.especie, p.raza, p.edad,p.tutor_id,
          t.nombre AS tutor_nombre, t.telefono
        FROM mascotas p
        JOIN tutores t ON p.tutor_id = t.id";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>