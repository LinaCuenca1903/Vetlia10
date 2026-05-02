<?php
include "conexion.php";

$mascota_id = isset($_GET['mascota_id']) ? $_GET['mascota_id'] : null;

$sql = "SELECT 
c.id,
m.nombre AS paciente,
m.especie,
c.fecha,
c.hora,
c.estado,
c.motivo
FROM citas c
JOIN mascotas m ON c.mascota_id = m.id";

if($mascota_id){
    $sql .= " WHERE c.mascota_id = ".intval($mascota_id);
}

$sql .= " ORDER BY c.fecha DESC, c.hora DESC";

$res = $conn->query($sql);

$data = [];

while($row = $res->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);