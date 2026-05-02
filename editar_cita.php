<?php
include "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$fecha = $data['fecha'];
$hora = $data['hora'];
$motivo = $data['motivo'];
$estado = $data['estado'];

$sql = "UPDATE citas SET 
fecha='$fecha',
hora='$hora',
motivo='$motivo',
estado='$estado'
WHERE id=$id";

if($conn->query($sql)){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}