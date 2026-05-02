<?php
header("Content-Type: application/json");

ini_set('display_errors', 0);
error_reporting(E_ALL);

try{

    $conn = new mysqli("localhost","root","","veterinaria");
    $conn->set_charset("utf8");

    if(empty($_GET['id'])){
        echo json_encode([]);
        exit;
    }

    $id = intval($_GET['id']);

    $sql = "
    SELECT 
    h.id,
    h.fecha,
    h.diagnostico,
    h.tratamiento,
    u.nombre AS veterinario
    FROM historias_clinicas h
    JOIN usuarios u ON h.usuario_id = u.id
    WHERE h.mascota_id = $id
    ORDER BY h.fecha DESC
    ";

    $res = $conn->query($sql);

    $data = [];

    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }

    echo json_encode($data);

}catch(Throwable $e){
    echo json_encode([]);
}