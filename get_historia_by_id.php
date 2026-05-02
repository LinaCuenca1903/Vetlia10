<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","veterinaria");
$conn->set_charset("utf8");

$id = intval($_GET['id']);

$sql = "
SELECT 
id,
fecha,
diagnostico,
tratamiento
FROM historias_clinicas
WHERE id = $id
";

$res = $conn->query($sql);

echo json_encode($res->fetch_assoc());