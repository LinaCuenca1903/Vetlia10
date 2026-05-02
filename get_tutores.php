<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

$sql = "
SELECT 
t.id,
t.nombre,
t.telefono,
t.direccion,
t.email,
GROUP_CONCAT(m.nombre SEPARATOR ', ') AS mascotas
FROM tutores t
LEFT JOIN mascotas m ON m.tutor_id = t.id
GROUP BY t.id
ORDER BY t.nombre ASC
";

$result = $conn->query($sql);

$tutores = [];

while ($row = $result->fetch_assoc()) {
    $tutores[] = $row;
}

echo json_encode($tutores);
?>